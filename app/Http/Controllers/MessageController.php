<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use App\Models\MediaLibrary;
use App\Models\MessageReaction;
use App\Models\ConversationTheme;
use App\Events\MessageUnreadCountUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get list of users for messages modal
     */
    public function getUsers()
    {
        try {
            $currentUserId = Auth::id();
            
            // Get all active users except the current user with agency relationship
            $users = User::where('is_active', true)
                ->where('id', '!=', $currentUserId)
                ->with('governmentAgency')
                ->select('id', 'first_name', 'last_name', 'is_online', 'last_activity', 'profile_picture', 'privilege', 'position', 'government_agency_id')
                ->orderBy('is_online', 'desc') // Online users first
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
            
            // Format users with profile picture URLs
            $formattedUsers = $users->map(function ($user) {
                $profilePictureUrl = null;
                
                if ($user->profile_picture) {
                    $media = \App\Models\MediaLibrary::find($user->profile_picture);
                    if ($media) {
                        $profilePictureUrl = asset('storage/' . $media->file_path);
                    }
                }
                
                // Fallback to UI Avatars if no profile picture
                if (!$profilePictureUrl) {
                    $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    $profilePictureUrl = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=64&background=055498&color=fff';
                }
                
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'is_online' => $user->is_online ?? false,
                    'last_activity' => $user->last_activity ? $user->last_activity->toIso8601String() : null,
                    'profile_picture_url' => $profilePictureUrl,
                    'privilege' => $user->privilege ?? null,
                    'position' => $user->position ?? null,
                    'agency' => $user->governmentAgency ? $user->governmentAgency->name : null,
                    'agency_id' => $user->government_agency_id,
                ];
            });
            
            return response()->json([
                'success' => true,
                'users' => $formattedUsers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required_without:group_id|exists:users,id',
                'group_id' => 'required_without:receiver_id|exists:group_chats,id',
                'message' => 'nullable|string|max:5000',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:25600', // 25MB max per file
                'parent_id' => 'nullable|exists:chats,id',
            ], [
                'attachments.*.max' => 'File size must not exceed 25MB. Please choose a smaller file.',
                'attachments.*.file' => 'Invalid file. Please select a valid file.',
                'receiver_id.required_without' => 'Either receiver_id or group_id is required.',
                'receiver_id.exists' => 'The selected receiver id is invalid.',
                'group_id.required_without' => 'Either receiver_id or group_id is required.',
                'group_id.exists' => 'The selected group id is invalid.',
            ]);

            $senderId = Auth::id();
            $receiverId = $request->receiver_id;
            $groupId = $request->group_id;
            $message = $request->message ?? '';
            $attachments = $request->file('attachments', []);
            $parentId = $request->parent_id;

            // If group message, verify user is a member
            if ($groupId) {
                $group = \App\Models\GroupChat::findOrFail($groupId);
                if (!$group->hasMember($senderId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not a member of this group',
                    ], 403);
                }
            }

            // Handle file attachments
            $attachmentIds = [];
            if (!empty($attachments)) {
                foreach ($attachments as $index => $file) {
                    try {
                        $mimeType = $file->getMimeType();
                        $originalName = $file->getClientOriginalName();
                        $fileSize = $file->getSize();
                        
                        // Check file size (25MB = 25600 KB = 26214400 bytes)
                        $maxSizeBytes = 25 * 1024 * 1024; // 25MB in bytes
                        if ($fileSize > $maxSizeBytes) {
                            $fileSizeMB = round($fileSize / (1024 * 1024), 2);
                            throw new \Exception("File '{$originalName}' is too large ({$fileSizeMB}MB). Maximum allowed: 25MB.");
                        }
                        
                        // Ensure the storage directory exists
                        $storagePath = storage_path('app/public/chat-attachments');
                        if (!file_exists($storagePath)) {
                            \Storage::disk('public')->makeDirectory('chat-attachments');
                        }
                        
                        // Store the file
                        $filePath = $file->store('chat-attachments', 'public');
                        if (!$filePath) {
                            throw new \Exception("Failed to store file '{$originalName}'. Please check storage permissions.");
                        }
                        
                        $fullPath = Storage::disk('public')->path($filePath);
                        
                        // Check if this is a voice message that needs MP3 conversion
                        $convertToMp3 = $request->has('convert_to_mp3') && 
                                       ($mimeType === 'video/mp4' || 
                                        $mimeType === 'audio/webm' || 
                                        $mimeType === 'audio/mp4' ||
                                        strpos($originalName, 'voice-message') !== false);
                        
                        if ($convertToMp3) {
                            // Try to convert to MP3 using FFmpeg
                            $mp3Path = $this->convertToMp3($fullPath, $filePath);
                            if ($mp3Path) {
                                // Conversion successful, use the new MP3 path
                                $filePath = $mp3Path;
                                $mimeType = 'audio/mpeg';
                                $originalName = preg_replace('/\.(webm|mp4|m4a)$/i', '.mp3', $originalName);
                            } else {
                                // FFmpeg not available - keep original format to avoid decoding errors
                                // Don't rename the file, keep it in its original format
                                // The browser will handle webm/mp4 audio files correctly
                                // Just update the MIME type to match the actual file content
                                if (strpos($mimeType, 'video/') === 0) {
                                    // If it's video/mp4 but it's actually audio, change to audio
                                    $mimeType = 'audio/mp4';
                                }
                                // Keep original extension and MIME type
                            }
                        }
                        
                        // Ensure file exists before getting size
                        if (!Storage::disk('public')->exists($filePath)) {
                            \Log::error('File not found after storage', ['file_path' => $filePath]);
                            throw new \Exception('File was not saved correctly');
                        }
                        
                        // Only use fillable fields from MediaLibrary model
                        // fillable: file_name, title, alt_text, caption, description, file_type, file_path, uploaded_by
                        $media = MediaLibrary::create([
                            'file_name' => $originalName,
                            'file_type' => $mimeType,
                            'file_path' => $filePath,
                            'uploaded_by' => $senderId,
                        ]);
                        
                        if (!$media || !$media->id) {
                            throw new \Exception('Failed to create media library entry');
                        }
                        
                        $attachmentIds[] = $media->id;
                    } catch (\Exception $fileException) {
                        \Log::error('Error processing attachment', [
                            'file_name' => $file->getClientOriginalName() ?? 'unknown',
                            'error' => $fileException->getMessage(),
                            'trace' => $fileException->getTraceAsString(),
                        ]);
                        throw new \Exception('Error processing file: ' . $fileException->getMessage());
                    }
                }
            }

            // Create chat message (unread by default)
            $chat = Chat::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'group_id' => $groupId,
                'parent_id' => $parentId,
                'message' => $message,
                'attachments' => !empty($attachmentIds) ? $attachmentIds : null,
                'timestamp' => now(),
                'is_read' => false,
            ]);

            // Load relationships
            $chat->load(['sender', 'receiver', 'group']);

            // Format the message for response
            $formattedMessage = $this->formatMessage($chat, $senderId);

            // Broadcast unread count updates to receiver(s)
            if ($groupId) {
                // Group message: broadcast to all group members except sender
                $group = \App\Models\GroupChat::findOrFail($groupId);
                $members = \App\Models\GroupMember::where('group_id', $groupId)
                    ->where('user_id', '!=', $senderId)
                    ->pluck('user_id');
                
                foreach ($members as $memberId) {
                    $unreadCount = $this->calculateUnreadCount($memberId);
                    broadcast(new MessageUnreadCountUpdated($memberId, $unreadCount));
                }
            } else {
                // Individual message: broadcast to receiver
                if ($receiverId) {
                    $unreadCount = $this->calculateUnreadCount($receiverId);
                    broadcast(new MessageUnreadCountUpdated($receiverId, $unreadCount));
                }
            }

            return response()->json([
                'success' => true,
                'message' => $formattedMessage,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors with user-friendly messages
            $errors = $e->errors();
            $errorMessage = 'Validation failed. ';
            
            // Extract the first error message
            foreach ($errors as $field => $messages) {
                if (!empty($messages)) {
                    $errorMessage = $messages[0];
                    break;
                }
            }
            
            \Log::warning('Message validation failed', [
                'errors' => $errors,
                'user_id' => Auth::id(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error sending message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            
            // Check if error message contains file size information
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, '25600') !== false || strpos($errorMessage, 'kilobytes') !== false) {
                $errorMessage = 'File size must not exceed 25MB. Please choose a smaller file.';
            } else {
                $errorMessage = 'Failed to send message: ' . $errorMessage;
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Get conversation history between current user and another user, or group chat
     */
    public function getConversation(Request $request, $userId)
    {
        try {
            $currentUserId = Auth::id();

            // Note: Messages are marked as read by the frontend when chat is expanded (not minimized)
            // This allows minimized chats to remain unread

            // Check if this is a group chat (userId starts with 'group_')
            $isGroupChat = str_starts_with($userId, 'group_');
            
            if ($isGroupChat) {
                // Extract group ID
                $groupId = (int) str_replace('group_', '', $userId);
                $group = \App\Models\GroupChat::findOrFail($groupId);
                
                // Verify user is a member
                if (!$group->hasMember($currentUserId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not a member of this group',
                    ], 403);
                }
                
                // Get all messages in the group
                $messages = Chat::where('group_id', $groupId)
                    ->with(['sender', 'group', 'reactions.user', 'parent'])
                    ->orderBy('timestamp', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                // Get messages where current user is sender or receiver with the other user
                $messages = Chat::where(function ($query) use ($currentUserId, $userId) {
                    $query->where('sender_id', $currentUserId)
                          ->where('receiver_id', $userId)
                          ->whereNull('group_id'); // Exclude group messages
                })->orWhere(function ($query) use ($currentUserId, $userId) {
                    $query->where('sender_id', $userId)
                          ->where('receiver_id', $currentUserId)
                          ->whereNull('group_id'); // Exclude group messages
                })
                ->with(['sender', 'receiver', 'reactions.user', 'parent'])
                ->orderBy('timestamp', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            }

            // Format messages
            $formattedMessages = $messages->map(function ($chat) use ($currentUserId) {
                $isSender = $chat->sender_id === $currentUserId;
                
                // Get attachment URLs if exists
                $attachmentUrls = [];
                if ($chat->attachments) {
                    $attachmentIds = is_array($chat->attachments) ? $chat->attachments : json_decode($chat->attachments, true);
                    if (is_array($attachmentIds)) {
                        foreach ($attachmentIds as $mediaId) {
                            $media = MediaLibrary::find($mediaId);
                            if ($media) {
                                // Use current request's base URL to ensure consistency
                                // This matches the domain used in the current request (localhost vs 127.0.0.1)
                                $baseUrl = request()->getSchemeAndHttpHost();
                                $fileUrl = $baseUrl . '/storage/' . $media->file_path;
                                
                                $attachmentUrls[] = [
                                    'id' => $media->id,
                                    'url' => $fileUrl,
                                    'name' => $media->file_name,
                                    'type' => $media->file_type,
                                ];
                            }
                        }
                    }
                }

                // Get sender profile picture
                $senderProfilePictureUrl = null;
                if ($chat->sender->profile_picture) {
                    $senderMedia = MediaLibrary::find($chat->sender->profile_picture);
                    if ($senderMedia) {
                        $senderProfilePictureUrl = asset('storage/' . $senderMedia->file_path);
                    }
                }

                // Get reactions
                $reactions = $chat->reactions->groupBy('reaction_type')->map(function ($group) {
                    return [
                        'type' => $group->first()->reaction_type,
                        'count' => $group->count(),
                        'users' => $group->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->first_name . ' ' . $reaction->user->last_name,
                            ];
                        })->toArray(),
                    ];
                })->values();

                // Get parent message info if this is a reply
                $parentMessage = null;
                if ($chat->parent_id && $chat->parent) {
                    $parentSenderName = $chat->parent->sender->first_name . ' ' . $chat->parent->sender->last_name;
                    $parentMessage = [
                        'id' => $chat->parent->id,
                        'message' => $chat->parent->message,
                        'sender_name' => $parentSenderName,
                        'sender_id' => $chat->parent->sender_id,
                    ];
                }

                return [
                    'id' => $chat->id,
                    'sender_id' => $chat->sender_id,
                    'receiver_id' => $chat->receiver_id,
                    'parent_id' => $chat->parent_id,
                    'message' => $chat->message,
                    'attachments' => $attachmentUrls,
                    'timestamp' => $chat->timestamp->toIso8601String(),
                    'created_at' => $chat->created_at->toIso8601String(),
                    'is_sender' => $isSender,
                    'reactions' => $reactions,
                    'parent_message' => $parentMessage,
                    'sender' => [
                        'id' => $chat->sender->id,
                        'first_name' => $chat->sender->first_name,
                        'last_name' => $chat->sender->last_name,
                        'profile_picture_url' => $senderProfilePictureUrl,
                        'is_online' => $chat->sender->is_online ?? false,
                    ],
                ];
            });

            // Encrypt the messages data using Laravel's encryption
            $responseData = [
                'success' => true,
                'messages' => $formattedMessages,
            ];
            
            $encryptedData = Crypt::encryptString(json_encode($responseData));

            return response()->json([
                'success' => true,
                'encrypted' => true,
                'data' => $encryptedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load conversation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Decrypt encrypted conversation data
     */
    public function decryptConversation(Request $request)
    {
        try {
            $encryptedData = $request->input('encrypted_data');
            
            if (!$encryptedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No encrypted data provided',
                ], 400);
            }

            // Decrypt the data
            $decryptedData = Crypt::decryptString($encryptedData);
            $data = json_decode($decryptedData, true);

            return response()->json([
                'success' => true,
                'messages' => $data['messages'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decrypt data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get conversations list (users the current user has chatted with)
     */
    public function getConversations()
    {
        try {
            $currentUserId = Auth::id();

            // Get distinct users the current user has chatted with (exclude group messages)
            $conversations = Chat::where(function ($query) use ($currentUserId) {
                    $query->where('sender_id', $currentUserId)
                          ->orWhere('receiver_id', $currentUserId);
                })
                ->whereNull('group_id') // Exclude group messages
                ->with(['sender', 'receiver'])
                ->orderBy('timestamp', 'desc')
                ->get()
                ->groupBy(function ($chat) use ($currentUserId) {
                    // Group by the other user's ID
                    return $chat->sender_id === $currentUserId 
                        ? $chat->receiver_id 
                        : $chat->sender_id;
                })
                ->map(function ($chats) use ($currentUserId) {
                    $latestChat = $chats->first();
                    
                    // Skip if no receiver or sender (shouldn't happen, but safety check)
                    if (!$latestChat->receiver || !$latestChat->sender) {
                        return null;
                    }
                    
                    $otherUser = $latestChat->sender_id === $currentUserId 
                        ? $latestChat->receiver 
                        : $latestChat->sender;
                    
                    // Get unread count (messages sent to current user that are unread, exclude group messages)
                    $unreadCount = Chat::where('sender_id', $otherUser->id)
                        ->where('receiver_id', $currentUserId)
                        ->whereNull('group_id') // Exclude group messages
                        ->where('is_read', false)
                        ->count();

                    // Get profile picture
                    $profilePictureUrl = null;
                    if ($otherUser->profile_picture) {
                        $media = MediaLibrary::find($otherUser->profile_picture);
                        if ($media) {
                            $profilePictureUrl = asset('storage/' . $media->file_path);
                        }
                    }
                    
                    // Get user initials
                    $initials = strtoupper(
                        substr($otherUser->first_name ?? '', 0, 1) . 
                        substr($otherUser->last_name ?? '', 0, 1)
                    );

                    // Format time ago
                    $timeAgo = '';
                    if ($latestChat->timestamp) {
                        $diff = now()->diffInMinutes($latestChat->timestamp);
                        if ($diff < 1) {
                            $timeAgo = 'Just now';
                        } elseif ($diff < 60) {
                            $timeAgo = $diff . 'm ago';
                        } elseif ($diff < 1440) {
                            $timeAgo = floor($diff / 60) . 'h ago';
                        } else {
                            $timeAgo = floor($diff / 1440) . 'd ago';
                        }
                    }

                    // Determine last message text
                    $lastMessageText = '';
                    if ($latestChat->message) {
                        $lastMessageText = strlen($latestChat->message) > 50 ? substr($latestChat->message, 0, 50) . '...' : $latestChat->message;
                    } else {
                        // Check for attachments
                        if ($latestChat->attachments) {
                            $attachmentIds = is_array($latestChat->attachments) ? $latestChat->attachments : json_decode($latestChat->attachments, true);
                            if (is_array($attachmentIds) && count($attachmentIds) > 0) {
                                // Get the first attachment to determine type
                                $firstAttachmentId = $attachmentIds[0];
                                $media = MediaLibrary::find($firstAttachmentId);
                                if ($media) {
                                    $fileType = $media->file_type ?? '';
                                    if (str_starts_with($fileType, 'image/')) {
                                        $lastMessageText = '📷 Image';
                                    } elseif (str_starts_with($fileType, 'video/')) {
                                        $lastMessageText = '🎥 Video';
                                    } elseif (str_starts_with($fileType, 'audio/')) {
                                        $lastMessageText = '🎤 Voice message';
                                    } else {
                                        $lastMessageText = '📎 ' . ($media->file_name ?? 'Attachment');
                                    }
                                } else {
                                    $lastMessageText = '📎 Attachment';
                                }
                            } else {
                                $lastMessageText = 'No messages yet';
                            }
                        } else {
                            $lastMessageText = 'No messages yet';
                        }
                    }

                    return [
                        'user_id' => $otherUser->id,
                        'user_name' => $otherUser->first_name . ' ' . $otherUser->last_name,
                        'user_initials' => $initials,
                        'profile_picture_url' => $profilePictureUrl,
                        'last_message' => $lastMessageText,
                        'last_message_time' => $latestChat->timestamp->toIso8601String(),
                        'time_ago' => $timeAgo,
                        'unread_count' => $unreadCount,
                        'is_online' => $otherUser->is_online ?? false,
                        'is_group' => false,
                    ];
                })
                ->filter() // Remove null entries
                ->values();
            
            // Get group chats the user is a member of
            $groupChats = \App\Models\GroupChat::whereHas('members', function ($query) use ($currentUserId) {
                    $query->where('user_id', $currentUserId);
                })
                ->with(['members.user', 'creator'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($group) use ($currentUserId) {
                    // Get latest message in group
                    $latestChat = Chat::where('group_id', $group->id)
                        ->orderBy('timestamp', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    // Get unread count (messages in group that current user hasn't read)
                    $unreadCount = Chat::where('group_id', $group->id)
                        ->where('sender_id', '!=', $currentUserId)
                        ->where('is_read', false)
                        ->count();
                    
                    // Get group avatar or use default
                    // Check if avatar is already a full URL (stored from asset() helper)
                    if ($group->avatar) {
                        if (filter_var($group->avatar, FILTER_VALIDATE_URL)) {
                            // Already a full URL, use as-is
                            $profilePictureUrl = $group->avatar;
                        } else {
                            // Relative path, prepend storage
                            $profilePictureUrl = asset('storage/' . $group->avatar);
                        }
                    } else {
                        $profilePictureUrl = null;
                    }
                    
                    if (!$profilePictureUrl) {
                        // Use group name initials
                        $initials = strtoupper(substr($group->name, 0, 2));
                        $profilePictureUrl = 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&size=64&background=055498&color=fff';
                    }
                    
                    // Format time ago
                    $timeAgo = '';
                    if ($latestChat && $latestChat->timestamp) {
                        $diff = now()->diffInMinutes($latestChat->timestamp);
                        if ($diff < 1) {
                            $timeAgo = 'Just now';
                        } elseif ($diff < 60) {
                            $timeAgo = $diff . 'm ago';
                        } elseif ($diff < 1440) {
                            $timeAgo = floor($diff / 60) . 'h ago';
                        } else {
                            $timeAgo = floor($diff / 1440) . 'd ago';
                        }
                    }
                    
                    // Determine last message text
                    $lastMessageText = '';
                    if ($latestChat) {
                        if ($latestChat->message) {
                            if ($latestChat->sender) {
                                $senderName = $latestChat->sender->first_name . ' ' . $latestChat->sender->last_name;
                                $lastMessageText = $senderName . ': ' . (strlen($latestChat->message) > 40 ? substr($latestChat->message, 0, 40) . '...' : $latestChat->message);
                            } else {
                                $lastMessageText = strlen($latestChat->message) > 50 ? substr($latestChat->message, 0, 50) . '...' : $latestChat->message;
                            }
                        } else {
                            // Check for attachments
                            if ($latestChat->attachments) {
                                $attachmentIds = is_array($latestChat->attachments) ? $latestChat->attachments : json_decode($latestChat->attachments, true);
                                if (is_array($attachmentIds) && count($attachmentIds) > 0) {
                                    $firstAttachmentId = $attachmentIds[0];
                                    $media = MediaLibrary::find($firstAttachmentId);
                                    if ($media) {
                                        $fileType = $media->file_type ?? '';
                                        if ($latestChat->sender) {
                                            $senderName = $latestChat->sender->first_name . ' ' . $latestChat->sender->last_name;
                                            if (str_starts_with($fileType, 'image/')) {
                                                $lastMessageText = $senderName . ': 📷 Image';
                                            } elseif (str_starts_with($fileType, 'video/')) {
                                                $lastMessageText = $senderName . ': 🎥 Video';
                                            } elseif (str_starts_with($fileType, 'audio/')) {
                                                $lastMessageText = $senderName . ': 🎤 Voice message';
                                            } else {
                                                $lastMessageText = $senderName . ': 📎 Attachment';
                                            }
                                        } else {
                                            // No sender, just show attachment type
                                            if (str_starts_with($fileType, 'image/')) {
                                                $lastMessageText = '📷 Image';
                                            } elseif (str_starts_with($fileType, 'video/')) {
                                                $lastMessageText = '🎥 Video';
                                            } elseif (str_starts_with($fileType, 'audio/')) {
                                                $lastMessageText = '🎤 Voice message';
                                            } else {
                                                $lastMessageText = '📎 Attachment';
                                            }
                                        }
                                    } else {
                                        $lastMessageText = 'No messages yet';
                                    }
                                } else {
                                    $lastMessageText = 'No messages yet';
                                }
                            } else {
                                $lastMessageText = 'No messages yet';
                            }
                        }
                    } else {
                        $lastMessageText = 'No messages yet';
                    }
                    
                    return [
                        'group_id' => $group->id,
                        'user_id' => 'group_' . $group->id, // Use group_ prefix for frontend
                        'user_name' => $group->name,
                        'user_initials' => strtoupper(substr($group->name, 0, 2)),
                        'profile_picture_url' => $profilePictureUrl,
                        'last_message' => $lastMessageText,
                        'last_message_time' => $latestChat ? $latestChat->timestamp->toIso8601String() : $group->created_at->toIso8601String(),
                        'time_ago' => $timeAgo,
                        'unread_count' => $unreadCount,
                        'is_online' => false, // Groups don't have online status
                        'is_group' => true,
                    ];
                });
            
            // Merge user and group conversations, sort by last message time (most recent first)
            $allConversations = $conversations->concat($groupChats)
                ->sortByDesc(function ($conv) {
                    // Use last_message_time for sorting, fallback to created_at for groups without messages
                    $sortTime = $conv['last_message_time'] ?? null;
                    if (!$sortTime && isset($conv['is_group']) && $conv['is_group']) {
                        // For groups without messages, use group created_at
                        $sortTime = $conv['created_at'] ?? now()->toIso8601String();
                    }
                    return $sortTime ?? '';
                })
                ->values();
                // Removed ->take(10) to show all conversations, sorted by most recent first

            return response()->json([
                'success' => true,
                'conversations' => $allConversations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load conversations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get new messages since a given timestamp
     */
    public function getNewMessages(Request $request, $userId)
    {
        try {
            $currentUserId = Auth::id();
            $since = $request->input('since');

            // Check if this is a group chat (userId starts with 'group_')
            $isGroupChat = str_starts_with($userId, 'group_');
            
            if ($isGroupChat) {
                // Extract group ID
                $groupId = (int) str_replace('group_', '', $userId);
                $group = \App\Models\GroupChat::findOrFail($groupId);
                
                // Verify user is a member
                if (!$group->hasMember($currentUserId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not a member of this group',
                    ], 403);
                }
                
                // Get new messages in the group
                $query = Chat::where('group_id', $groupId);
                
                if ($since) {
                    $query->where('created_at', '>', $since);
                }
                
                $messages = $query->with(['sender', 'group', 'reactions.user', 'parent'])
                    ->orderBy('timestamp', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                // Individual chat
                $query = Chat::where(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $currentUserId)
                      ->where('receiver_id', $userId)
                      ->whereNull('group_id'); // Exclude group messages
                })->orWhere(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $currentUserId)
                      ->whereNull('group_id'); // Exclude group messages
                });

                if ($since) {
                    $query->where('created_at', '>', $since);
                }

                $messages = $query->with(['sender', 'receiver', 'reactions.user', 'parent'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            // Format messages
            $formattedMessages = $messages->map(function ($chat) use ($currentUserId) {
                $isSender = $chat->sender_id === $currentUserId;
                
                // Get attachment URLs if exists
                $attachmentUrls = [];
                if ($chat->attachments) {
                    $attachmentIds = is_array($chat->attachments) ? $chat->attachments : json_decode($chat->attachments, true);
                    if (is_array($attachmentIds)) {
                        foreach ($attachmentIds as $mediaId) {
                            $media = MediaLibrary::find($mediaId);
                            if ($media) {
                                // Use current request's base URL to ensure consistency
                                // This matches the domain used in the current request (localhost vs 127.0.0.1)
                                $baseUrl = request()->getSchemeAndHttpHost();
                                $fileUrl = $baseUrl . '/storage/' . $media->file_path;
                                
                                $attachmentUrls[] = [
                                    'id' => $media->id,
                                    'url' => $fileUrl,
                                    'name' => $media->file_name,
                                    'type' => $media->file_type,
                                ];
                            }
                        }
                    }
                }

                // Get sender profile picture
                $senderProfilePictureUrl = null;
                if ($chat->sender->profile_picture) {
                    $senderMedia = MediaLibrary::find($chat->sender->profile_picture);
                    if ($senderMedia) {
                        $senderProfilePictureUrl = asset('storage/' . $senderMedia->file_path);
                    }
                }

                // Get reactions
                $reactions = $chat->reactions->groupBy('reaction_type')->map(function ($group) {
                    return [
                        'type' => $group->first()->reaction_type,
                        'count' => $group->count(),
                        'users' => $group->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->first_name . ' ' . $reaction->user->last_name,
                            ];
                        })->toArray(),
                    ];
                })->values();

                // Get parent message info if this is a reply
                $parentMessage = null;
                if ($chat->parent_id && $chat->parent) {
                    $parentSenderName = $chat->parent->sender->first_name . ' ' . $chat->parent->sender->last_name;
                    $parentMessage = [
                        'id' => $chat->parent->id,
                        'message' => $chat->parent->message,
                        'sender_name' => $parentSenderName,
                        'sender_id' => $chat->parent->sender_id,
                    ];
                }

                return [
                    'id' => $chat->id,
                    'sender_id' => $chat->sender_id,
                    'receiver_id' => $chat->receiver_id,
                    'group_id' => $chat->group_id,
                    'parent_id' => $chat->parent_id,
                    'message' => $chat->message,
                    'attachments' => $attachmentUrls,
                    'timestamp' => $chat->timestamp->toIso8601String(),
                    'created_at' => $chat->created_at->toIso8601String(),
                    'is_sender' => $isSender,
                    'reactions' => $reactions,
                    'parent_message' => $parentMessage,
                    'sender' => [
                        'id' => $chat->sender->id,
                        'first_name' => $chat->sender->first_name,
                        'last_name' => $chat->sender->last_name,
                        'profile_picture_url' => $senderProfilePictureUrl,
                        'is_online' => $chat->sender->is_online ?? false,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'messages' => $formattedMessages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load new messages: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark messages as read for a conversation
     */
    public function markAsRead(Request $request, $userId)
    {
        try {
            $currentUserId = Auth::id();

            // Check if this is a group chat (userId starts with 'group_')
            $isGroupChat = str_starts_with($userId, 'group_');
            
            if ($isGroupChat) {
                // Extract group ID
                $groupId = (int) str_replace('group_', '', $userId);
                
                // Verify user is a member of the group
                $group = \App\Models\GroupChat::find($groupId);
                if (!$group || !$group->hasMember($currentUserId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not a member of this group',
                    ], 403);
                }
                
                // Mark all unread messages in the group (excluding messages sent by current user) as read
                $updated = Chat::where('group_id', $groupId)
                    ->where('sender_id', '!=', $currentUserId)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
            } else {
                // Individual chat: Mark all unread messages from this user as read
                $updated = Chat::where('sender_id', $userId)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->whereNull('group_id') // Exclude group messages
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
            }

            // Broadcast unread count update to current user
            $unreadCount = $this->calculateUnreadCount($currentUserId);
            broadcast(new MessageUnreadCountUpdated($currentUserId, $unreadCount));

            return response()->json([
                'success' => true,
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate unread message count for a user
     */
    private function calculateUnreadCount($userId)
    {
        // Count unread messages from individual chats
        $individualUnreadCount = Chat::where('receiver_id', $userId)
            ->where('is_read', false)
            ->whereNull('group_id')
            ->count();

        // Count unread messages from group chats
        $groupIds = \App\Models\GroupMember::where('user_id', $userId)
            ->pluck('group_id')
            ->toArray();

        $groupUnreadCount = 0;
        if (!empty($groupIds)) {
            $groupUnreadCount = Chat::whereIn('group_id', $groupIds)
                ->where('sender_id', '!=', $userId)
                ->where('is_read', false)
                ->count();
        }

        return $individualUnreadCount + $groupUnreadCount;
    }

    /**
     * Get total unread message count
     */
    public function getUnreadCount()
    {
        try {
            $currentUserId = Auth::id();
            $totalUnreadCount = $this->calculateUnreadCount($currentUserId);

            return response()->json([
                'success' => true,
                'count' => $totalUnreadCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
            ]);
        }
    }

    /**
     * React to a message
     */
    public function react(Request $request, $id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $userId = Auth::id();
            $reactionType = $request->input('reaction_type', 'like');

            // Validate reaction type
            $validReactions = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
            if (!in_array($reactionType, $validReactions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid reaction type',
                ], 400);
            }

            // Check if user already reacted
            $existingReaction = MessageReaction::where('chat_id', $id)
                ->where('user_id', $userId)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->reaction_type === $reactionType) {
                    // Remove reaction if clicking the same reaction
                    $existingReaction->delete();
                    return response()->json([
                        'success' => true,
                        'action' => 'removed',
                        'reactions' => $this->getMessageReactions($id),
                    ]);
                } else {
                    // Update reaction type
                    $existingReaction->reaction_type = $reactionType;
                    $existingReaction->save();
                }
            } else {
                // Create new reaction
                MessageReaction::create([
                    'chat_id' => $id,
                    'user_id' => $userId,
                    'reaction_type' => $reactionType,
                ]);
            }

            return response()->json([
                'success' => true,
                'action' => 'added',
                'reactions' => $this->getMessageReactions($id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to react: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, $id)
    {
        try {
            $parentMessage = Chat::findOrFail($id);
            $userId = Auth::id();
            $message = $request->input('message', '');
            $receiverId = $parentMessage->sender_id === $userId 
                ? $parentMessage->receiver_id 
                : $parentMessage->sender_id;

            // Handle file attachments
            $attachmentIds = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('media', $fileName, 'public');
                    
                    $media = MediaLibrary::create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_path' => $filePath,
                        'uploaded_by' => $userId,
                    ]);
                    $attachmentIds[] = $media->id;
                }
            }

            // Create reply message
            $reply = Chat::create([
                'sender_id' => $userId,
                'receiver_id' => $receiverId,
                'parent_id' => $id,
                'message' => $message,
                'attachments' => !empty($attachmentIds) ? $attachmentIds : null,
                'timestamp' => now(),
                'is_read' => false,
            ]);

            $reply->load(['sender', 'receiver', 'parent']);

            // Format reply for response
            $formattedReply = $this->formatMessage($reply, $userId);

            return response()->json([
                'success' => true,
                'message' => $formattedReply,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a message (soft delete)
     */
    public function delete($id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $userId = Auth::id();

            // Only allow sender to delete their own message
            if ($chat->sender_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own messages',
                ], 403);
            }

            $chat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed reactions for a message (with user info)
     */
    public function getReactions($id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $currentUserId = Auth::id();
            
            $reactions = MessageReaction::where('chat_id', $id)
                ->with('user:id,first_name,last_name,profile_picture')
                ->get()
                ->groupBy('reaction_type')
                ->map(function ($group, $type) use ($currentUserId) {
                    return [
                        'type' => $type,
                        'count' => $group->count(),
                        'users' => $group->map(function ($reaction) use ($currentUserId) {
                            $user = $reaction->user;
                            $profilePictureUrl = null;
                            if ($user->profile_picture) {
                                $media = MediaLibrary::find($user->profile_picture);
                                if ($media) {
                                    $profilePictureUrl = asset('storage/' . $media->file_path);
                                }
                            }
                            
                            if (!$profilePictureUrl) {
                                $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                                $profilePictureUrl = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=64&background=055498&color=fff';
                            }
                            
                            return [
                                'id' => $reaction->user->id,
                                'name' => $user->first_name . ' ' . $user->last_name,
                                'profile_picture_url' => $profilePictureUrl,
                                'is_current_user' => $reaction->user_id === $currentUserId,
                                'reaction_id' => $reaction->id,
                            ];
                        })->toArray(),
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'reactions' => $reactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reactions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get reactions for multiple messages (batch)
     */
    public function getBatchReactions()
    {
        try {
            $messageIds = request()->input('message_ids', []);
            
            if (empty($messageIds) || !is_array($messageIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid message IDs',
                ], 400);
            }
            
            // Convert message IDs to integers if they're strings
            $messageIds = array_map('intval', $messageIds);
            
            // Get all reactions for the given message IDs (chat_id is the message ID in MessageReaction)
            $reactions = \App\Models\MessageReaction::whereIn('chat_id', $messageIds)
                ->get()
                ->groupBy('chat_id')
                ->map(function ($chatReactions) {
                    return $chatReactions->groupBy('reaction_type')
                        ->map(function ($group, $type) {
                            return [
                                'type' => $type,
                                'count' => $group->count(),
                            ];
                        })
                        ->values();
                });
            
            // Ensure all message IDs are in the response (even if they have no reactions)
            $result = [];
            foreach ($messageIds as $messageId) {
                $result[$messageId] = $reactions->get($messageId, []);
            }
            
            return response()->json([
                'success' => true,
                'reactions' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reactions: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Convert audio/video file to MP3 using FFmpeg
     */
    private function convertToMp3($inputPath, $originalStoragePath)
    {
        // Check if FFmpeg is available
        $ffmpegPath = $this->findFFmpeg();
        if (!$ffmpegPath) {
            return null; // FFmpeg not available
        }
        
        try {
            // Generate output path (same directory as input)
            $outputPath = preg_replace('/\.(webm|mp4|m4a|ogg)$/i', '.mp3', $inputPath);
            $outputStoragePath = preg_replace('/\.(webm|mp4|m4a|ogg)$/i', '.mp3', $originalStoragePath);
            
            // Convert using FFmpeg
            // -i: input file
            // -acodec libmp3lame: use MP3 encoder
            // -ab 128k: audio bitrate 128kbps
            // -ar 44100: sample rate 44.1kHz
            // -ac 2: stereo (2 channels)
            // -y: overwrite output file if exists
            $command = escapeshellarg($ffmpegPath) . 
                      ' -i ' . escapeshellarg($inputPath) . 
                      ' -acodec libmp3lame -ab 128k -ar 44100 -ac 2 -y ' . 
                      escapeshellarg($outputPath) . ' 2>&1';
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($outputPath)) {
                // Delete original file from storage
                if (Storage::disk('public')->exists($originalStoragePath)) {
                    Storage::disk('public')->delete($originalStoragePath);
                }
                return $outputStoragePath;
            } else {
                // Log FFmpeg errors for debugging
                \Log::warning('FFmpeg conversion failed', [
                    'command' => $command,
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('FFmpeg conversion exception: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Find FFmpeg executable path
     */
    private function findFFmpeg()
    {
        $possiblePaths = [
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
            'ffmpeg', // In PATH
            '/opt/homebrew/bin/ffmpeg', // macOS Homebrew
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path === 'ffmpeg') {
                // Check if ffmpeg is in PATH
                exec('which ffmpeg 2>&1', $output, $returnCode);
                if ($returnCode === 0) {
                    return 'ffmpeg';
                }
            } else {
                if (file_exists($path) && is_executable($path)) {
                    return $path;
                }
            }
        }
        
        return null;
    }
    
    private function getMessageReactions($chatId)
    {
        $reactions = MessageReaction::where('chat_id', $chatId)
            ->with('user:id,first_name,last_name')
            ->get()
            ->groupBy('reaction_type')
            ->map(function ($group) {
                return [
                    'type' => $group->first()->reaction_type,
                    'count' => $group->count(),
                    'users' => $group->map(function ($reaction) {
                        return [
                            'id' => $reaction->user->id,
                            'name' => $reaction->user->first_name . ' ' . $reaction->user->last_name,
                        ];
                    })->toArray(),
                ];
            })
            ->values();

        return $reactions;
    }

    /**
     * Format a single message for response
     */
    private function formatMessage($chat, $currentUserId)
    {
        $isSender = $chat->sender_id === $currentUserId;
        
        // Get attachment URLs if exists
        $attachmentUrls = [];
        if ($chat->attachments) {
            $attachmentIds = is_array($chat->attachments) ? $chat->attachments : json_decode($chat->attachments, true);
            if (is_array($attachmentIds)) {
                foreach ($attachmentIds as $mediaId) {
                    $media = MediaLibrary::find($mediaId);
                    if ($media) {
                        $attachmentUrls[] = [
                            'id' => $media->id,
                            'url' => asset('storage/' . $media->file_path),
                            'name' => $media->file_name,
                            'type' => $media->file_type,
                        ];
                    }
                }
            }
        }

        // Get sender profile picture
        $senderProfilePictureUrl = null;
        if ($chat->sender->profile_picture) {
            $senderMedia = MediaLibrary::find($chat->sender->profile_picture);
            if ($senderMedia) {
                $senderProfilePictureUrl = asset('storage/' . $senderMedia->file_path);
            }
        }

        // Get reactions
        $reactions = $this->getMessageReactions($chat->id);

        // Get parent message info if this is a reply
        $parentMessage = null;
        if ($chat->parent_id && $chat->parent) {
            $parentSenderName = $chat->parent->sender->first_name . ' ' . $chat->parent->sender->last_name;
            $parentMessage = [
                'id' => $chat->parent->id,
                'message' => $chat->parent->message,
                'sender_name' => $parentSenderName,
            ];
        }

        return [
            'id' => $chat->id,
            'sender_id' => $chat->sender_id,
            'receiver_id' => $chat->receiver_id,
            'parent_id' => $chat->parent_id,
            'message' => $chat->message,
            'attachments' => $attachmentUrls,
            'timestamp' => $chat->timestamp->toIso8601String(),
            'created_at' => $chat->created_at->toIso8601String(),
            'is_sender' => $isSender,
            'reactions' => $reactions,
            'parent_message' => $parentMessage,
            'sender' => [
                'id' => $chat->sender->id,
                'first_name' => $chat->sender->first_name,
                'last_name' => $chat->sender->last_name,
                'profile_picture_url' => $senderProfilePictureUrl,
                'is_online' => $chat->sender->is_online ?? false,
            ],
        ];
    }

    /**
     * Get available themes for single chats
     */
    public function getThemes()
    {
        try {
            $themes = $this->getAvailableThemes();
            
            return response()->json([
                'success' => true,
                'themes' => $themes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load themes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get theme for a conversation
     */
    public function getConversationTheme($otherUserId)
    {
        try {
            $currentUserId = Auth::id();
            $theme = ConversationTheme::getThemeForConversation($currentUserId, $otherUserId);
            
            return response()->json([
                'success' => true,
                'theme' => $theme,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get theme: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply theme to a conversation
     */
    public function applyConversationTheme(Request $request, $otherUserId)
    {
        try {
            $currentUserId = Auth::id();

            $validator = Validator::make($request->all(), [
                'theme' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Validate theme exists
            $availableThemes = $this->getAvailableThemes();
            $themeIds = array_column($availableThemes, 'id');
            if (!in_array($request->theme, $themeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid theme selected',
                ], 422);
            }

            // Get or create conversation theme
            $conversationTheme = ConversationTheme::getOrCreateForConversation($currentUserId, $otherUserId);
            $conversationTheme->update(['theme' => $request->theme]);

            return response()->json([
                'success' => true,
                'message' => 'Theme applied successfully',
                'theme' => $request->theme,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply theme: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available themes configuration (same as GroupChatController)
     */
    private function getAvailableThemes(): array
    {
        $themes = [
            // Background Image Themes (First)
            [
                'id' => 'fiery_red',
                'name' => 'Fiery Red',
                'description' => 'Intense red and orange with dark background',
                'background' => '#31160b',
                'background_image' => asset('images/background_chat/avatar.jpg'),
                'sender_bubble' => '#ff2424',
                'receiver_bubble' => '#3b2b2b',
                'sender_text' => '#000000',
                'receiver_text' => '#ffffff',
                'accent_color' => '#ff6633',
                'header_color' => '#310907',
                'icon_color' => '#ffa842',
            ],
            [
                'id' => 'chill_blue',
                'name' => 'Chill Blue',
                'description' => 'Calming blue tones with dark atmosphere',
                'background' => '#13172e',
                'background_image' => asset('images/background_chat/chill.jpg'),
                'sender_bubble' => '#499aeb',
                'receiver_bubble' => '#313e58',
                'sender_text' => '#000000',
                'receiver_text' => '#ffffff',
                'accent_color' => '#6ca4eb',
                'header_color' => '#263a63',
                'icon_color' => '#8faeeb',
            ],
            [
                'id' => 'cyan_friends',
                'name' => 'Cyan Friends',
                'description' => 'Fresh cyan and teal with oceanic vibes',
                'background' => '#006570',
                'background_image' => asset('images/background_chat/shapefriends.jpg'),
                'sender_bubble' => '#b6e8ff',
                'receiver_bubble' => '#003155',
                'sender_text' => '#000000',
                'receiver_text' => '#ffffff',
                'accent_color' => '#b6e8ff',
                'header_color' => '#006570',
                'icon_color' => '#b6e8ff',
            ],
            [
                'id' => 'kpop_purple',
                'name' => 'K-Pop Purple',
                'description' => 'Vibrant purple with deep dark background',
                'background' => '#01023a',
                'background_image' => asset('images/background_chat/kpop.jpg'),
                'sender_bubble' => '#a67fff',
                'receiver_bubble' => '#231479',
                'sender_text' => '#000000',
                'receiver_text' => '#ffffff',
                'accent_color' => '#a67fff',
                'header_color' => '#1f0138',
                'icon_color' => '#4c76f5',
            ],
            [
                'id' => 'tron_red',
                'name' => 'Tron Red',
                'description' => 'Futuristic black and red with neon accents',
                'background' => '#010101',
                'background_image' => asset('images/background_chat/tron.jpg'),
                'sender_bubble' => '#e0d6d1',
                'receiver_bubble' => '#202020',
                'sender_text' => '#000000',
                'receiver_text' => '#ffffff',
                'accent_color' => '#fe0000',
                'header_color' => '#010101',
                'icon_color' => '#fe0000',
            ],
            [
                'id' => 'taylor_green',
                'name' => 'Taylor Green',
                'description' => 'Mint green with warm brown tones',
                'background' => '#471a0a',
                'background_image' => asset('images/background_chat/taylor.jpg'),
                'sender_bubble' => '#96c6a8',
                'receiver_bubble' => '#3b2e29',
                'sender_text' => '#000000',
                'receiver_text' => '#ffffff',
                'accent_color' => '#96c6a8',
                'header_color' => '#a24627',
                'icon_color' => '#96c6a8',
            ],
            // Color and Gradient Themes (Second)
            [
                'id' => 'default',
                'name' => 'Default',
                'description' => 'Standard messaging appearance',
                'background' => '#f0f2f5',
                'sender_bubble' => '#055498',
                'receiver_bubble' => '#ffffff',
                'sender_text' => '#ffffff',
                'receiver_text' => '#1f2937',
                'accent_color' => '#055498',
                'header_color' => '#055498',
                'icon_color' => '#055498',
            ],
            [
                'id' => 'classic',
                'name' => 'Classic Blue',
                'description' => 'Professional and corporate default theme',
                'background' => '#f5f7fa',
                'sender_bubble' => '#1e3a8a',
                'receiver_bubble' => '#ffffff',
                'sender_text' => '#ffffff',
                'receiver_text' => '#1f2937',
                'accent_color' => '#1e3a8a',
                'header_color' => '#1e3a8a',
                'icon_color' => '#1e3a8a',
            ],
            [
                'id' => 'slate',
                'name' => 'Slate Gray',
                'description' => 'Modern neutral tones for formal discussions',
                'background' => '#f1f5f9',
                'sender_bubble' => '#334155',
                'receiver_bubble' => '#ffffff',
                'sender_text' => '#ffffff',
                'receiver_text' => '#1f2937',
                'accent_color' => '#334155',
                'header_color' => '#334155',
                'icon_color' => '#334155',
            ],
            [
                'id' => 'teal',
                'name' => 'Teal Professional',
                'description' => 'Calm and balanced professional palette',
                'background' => '#f0fdfa',
                'sender_bubble' => '#0f766e',
                'receiver_bubble' => '#ffffff',
                'sender_text' => '#ffffff',
                'receiver_text' => '#1f2937',
                'accent_color' => '#0f766e',
                'header_color' => '#0f766e',
                'icon_color' => '#0f766e',
            ],
            [
                'id' => 'olive',
                'name' => 'Olive Green',
                'description' => 'Subtle and composed for long discussions',
                'background' => '#f7f8f3',
                'sender_bubble' => '#3f6212',
                'receiver_bubble' => '#ffffff',
                'sender_text' => '#ffffff',
                'receiver_text' => '#1f2937',
                'accent_color' => '#3f6212',
                'header_color' => '#3f6212',
                'icon_color' => '#3f6212',
            ],
            [
                'id' => 'plum',
                'name' => 'Muted Plum',
                'description' => 'Reserved elegance with soft contrast',
                'background' => '#faf5ff',
                'sender_bubble' => '#6b21a8',
                'receiver_bubble' => '#ffffff',
                'sender_text' => '#ffffff',
                'receiver_text' => '#1f2937',
                'accent_color' => '#6b21a8',
                'header_color' => '#6b21a8',
                'icon_color' => '#6b21a8',
            ],
            [
                'id' => 'dark_pro',
                'name' => 'Dark Professional',
                'description' => 'Low-light professional interface',
                'background' => '#0f172a',
                'sender_bubble' => '#2563eb',
                'receiver_bubble' => '#1e293b',
                'sender_text' => '#ffffff',
                'receiver_text' => '#e5e7eb',
                'accent_color' => '#2563eb',
                'header_color' => '#020617',
                'icon_color' => '#93c5fd',
            ],
        ];

        return $themes;
    }
}

