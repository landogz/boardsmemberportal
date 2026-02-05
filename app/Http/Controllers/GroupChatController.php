<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GroupChatController extends Controller
{
    /**
     * Create a new group chat
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'member_ids' => 'required|array|min:1',
                'member_ids.*' => 'required|uuid|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $currentUserId = Auth::id();

            // Ensure creator is included in members
            $memberIds = array_unique(array_merge([$currentUserId], $request->member_ids));

            DB::beginTransaction();

            // Create the group
            $group = GroupChat::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => $currentUserId,
            ]);

            // Add members
            foreach ($memberIds as $memberId) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $memberId,
                    'is_admin' => $memberId === $currentUserId, // Creator is admin
                    'joined_at' => now(),
                ]);
            }

            DB::commit();

            // Load relationships for response
            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Group chat created successfully',
                'group' => $this->formatGroupChat($group),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group chat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update group chat details (admin only)
     */
    public function update(Request $request, $groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can update group details',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string|max:1000',
                // Allow avatar to be null or any type (string URL or null); we validate usage below
                'avatar' => 'nullable',
                'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'theme' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $updateData = [];
            
            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }
            if ($request->has('description')) {
                $updateData['description'] = $request->description;
            }
            if ($request->has('theme')) {
                $updateData['theme'] = $request->theme;
            }
            
            // Handle avatar file upload
            if ($request->hasFile('avatar_file')) {
                $file = $request->file('avatar_file');
                $fileName = \Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'group-avatars/' . $fileName;
                
                // Create directory if it doesn't exist
                if (!\Storage::disk('public')->exists('group-avatars')) {
                    \Storage::disk('public')->makeDirectory('group-avatars');
                }
                
                // Delete old avatar if exists (check if it's a local file)
                if ($group->avatar) {
                    // Check if avatar is a local storage path
                    $avatarPath = str_replace(asset('storage/'), '', $group->avatar);
                    if (\Storage::disk('public')->exists($avatarPath)) {
                        \Storage::disk('public')->delete($avatarPath);
                    }
                }
                
                // Upload new avatar
                \Storage::disk('public')->put($filePath, file_get_contents($file));
                $updateData['avatar'] = asset('storage/' . $filePath);
            } elseif ($request->has('avatar') && $request->avatar === null) {
                // Remove avatar
                if ($group->avatar) {
                    // Check if avatar is a local storage path
                    $avatarPath = str_replace(asset('storage/'), '', $group->avatar);
                    if (\Storage::disk('public')->exists($avatarPath)) {
                        \Storage::disk('public')->delete($avatarPath);
                    }
                }
                $updateData['avatar'] = null;
            } elseif ($request->has('avatar') && is_string($request->avatar) && $request->avatar !== '') {
                // Avatar URL provided
                $updateData['avatar'] = $request->avatar;
            }
            
            if (!empty($updateData)) {
                $group->update($updateData);
            }
            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Group chat updated successfully',
                'group' => $this->formatGroupChat($group),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update group chat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get group chat details
     */
    public function show($groupId)
    {
        try {
            $group = GroupChat::with(['members.user', 'creator'])->findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is a member
            if (!$group->hasMember($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'group' => $this->formatGroupChat($group),
                'is_admin' => $group->isAdmin($currentUserId),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load group chat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all groups for current user
     */
    public function index()
    {
        try {
            $currentUserId = Auth::id();

            $groups = GroupChat::whereHas('members', function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId);
            })
            ->with(['members.user', 'creator'])
            ->orderBy('updated_at', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'groups' => $groups->map(function ($group) {
                    return $this->formatGroupChat($group);
                }),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load group chats: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add members to group (admin only)
     */
    public function addMembers(Request $request, $groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can add members',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|uuid|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $addedMembers = [];
            foreach ($request->user_ids as $userId) {
                // Skip if already a member
                if ($group->hasMember($userId)) {
                    continue;
                }

                $member = GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $userId,
                    'is_admin' => false,
                    'joined_at' => now(),
                ]);

                $addedMembers[] = $member->load('user');
            }

            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => count($addedMembers) . ' member(s) added successfully',
                'group' => $this->formatGroupChat($group),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add members: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove members from group (admin only)
     */
    public function removeMembers(Request $request, $groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can remove members',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|uuid|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Prevent removing the last admin
            $adminCount = $group->admins()->count();
            $removingAdmins = GroupMember::where('group_id', $group->id)
                ->whereIn('user_id', $request->user_ids)
                ->where('is_admin', true)
                ->count();

            if ($adminCount - $removingAdmins < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove all admins. At least one admin must remain.',
                ], 422);
            }

            // Prevent removing yourself if you're the only admin
            if (in_array($currentUserId, $request->user_ids) && $adminCount === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove yourself as the only admin. Assign another admin first.',
                ], 422);
            }

            GroupMember::where('group_id', $group->id)
                ->whereIn('user_id', $request->user_ids)
                ->delete();

            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Member(s) removed successfully',
                'group' => $this->formatGroupChat($group),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove members: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign admin privileges (admin only)
     */
    public function assignAdmin(Request $request, $groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can assign admin privileges',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|uuid|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Ensure users are members
            foreach ($request->user_ids as $userId) {
                if (!$group->hasMember($userId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not a member of this group',
                    ], 422);
                }
            }

            GroupMember::where('group_id', $group->id)
                ->whereIn('user_id', $request->user_ids)
                ->update(['is_admin' => true]);

            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Admin privileges assigned successfully',
                'group' => $this->formatGroupChat($group),
                'is_admin' => $group->isAdmin($currentUserId),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign admin privileges: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Revoke admin privileges (admin only)
     */
    public function revokeAdmin(Request $request, $groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can revoke admin privileges',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|uuid|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Prevent revoking from yourself if you're the only admin
            $adminCount = $group->admins()->count();
            if (in_array($currentUserId, $request->user_ids) && $adminCount === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot revoke your own admin privileges as the only admin',
                ], 422);
            }

            // Ensure at least one admin remains
            $revokingAdmins = GroupMember::where('group_id', $group->id)
                ->whereIn('user_id', $request->user_ids)
                ->where('is_admin', true)
                ->count();

            if ($adminCount - $revokingAdmins < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one admin must remain in the group',
                ], 422);
            }

            GroupMember::where('group_id', $group->id)
                ->whereIn('user_id', $request->user_ids)
                ->update(['is_admin' => false]);

            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Admin privileges revoked successfully',
                'group' => $this->formatGroupChat($group),
                'is_admin' => $group->isAdmin($currentUserId),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke admin privileges: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Leave group (member can leave themselves)
     */
    public function leave($groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is a member
            if (!$group->hasMember($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group',
                ], 403);
            }

            // Prevent leaving if you're the only admin
            if ($group->isAdmin($currentUserId)) {
                $adminCount = $group->admins()->count();
                if ($adminCount === 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot leave as the only admin. Assign another admin or delete the group.',
                    ], 422);
                }
            }

            GroupMember::where('group_id', $group->id)
                ->where('user_id', $currentUserId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'You have left the group successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete group (admin only)
     */
    public function destroy($groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can delete the group',
                ], 403);
            }

            $group->delete();

            return response()->json([
                'success' => true,
                'message' => 'Group deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete group: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format group chat for API response
     */
    private function formatGroupChat(GroupChat $group): array
    {
        $currentUserId = Auth::id();
        
        return [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'created_by' => $group->created_by,
            'creator' => [
                'id' => $group->creator->id,
                'first_name' => $group->creator->first_name,
                'last_name' => $group->creator->last_name,
                'profile_picture_url' => $this->getProfilePictureUrl($group->creator),
            ],
            'avatar' => $group->avatar,
            'theme' => $group->theme ?? 'default',
            'member_count' => $group->member_count,
            'members' => $group->members->map(function ($member) {
                return [
                    'id' => $member->user->id,
                    'first_name' => $member->user->first_name,
                    'last_name' => $member->user->last_name,
                    'profile_picture_url' => $this->getProfilePictureUrl($member->user),
                    'is_admin' => $member->is_admin,
                    'joined_at' => $member->joined_at->toIso8601String(),
                ];
            }),
            'is_admin' => $group->isAdmin($currentUserId),
            'is_member' => $group->hasMember($currentUserId),
            'created_at' => $group->created_at->toIso8601String(),
            'updated_at' => $group->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get available themes
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
     * Apply theme to group chat (admin only)
     */
    public function applyTheme(Request $request, $groupId)
    {
        try {
            $group = GroupChat::findOrFail($groupId);
            $currentUserId = Auth::id();

            // Check if user is admin
            if (!$group->isAdmin($currentUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only group admins can change the theme',
                ], 403);
            }

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

            $group->update(['theme' => $request->theme]);
            $group->load(['members.user', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Theme applied successfully',
                'group' => $this->formatGroupChat($group),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply theme: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if a color is light (returns true) or dark (returns false)
     * Uses YIQ formula for brightness calculation
     * Lower threshold to better detect dark/black backgrounds
     */
    private function isLightColor(string $color): bool
    {
        // Remove spaces and convert to lowercase
        $color = strtolower(trim($color));
        
        // Check for common dark/black color names
        $darkColors = ['black', '#000', '#000000', 'rgb(0,0,0)', 'rgba(0,0,0'];
        foreach ($darkColors as $darkColor) {
            if (strpos($color, $darkColor) !== false) {
                return false; // Definitely dark
            }
        }
        
        // Handle hex colors
        if (strpos($color, '#') === 0) {
            $hex = str_replace('#', '', $color);
            // Handle 3-digit hex
            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            if (strlen($hex) === 6) {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                
                // Check if it's very dark (close to black)
                if ($r < 50 && $g < 50 && $b < 50) {
                    return false; // Very dark, use white text
                }
                
                // YIQ formula: brightness threshold lowered to 150 for better dark detection
                $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                return $yiq > 150; // Lower threshold to catch more dark colors
            }
        }
        
        // Handle rgb/rgba colors
        if (preg_match('/rgba?\((\d+),\s*(\d+),\s*(\d+)/', $color, $matches)) {
            $r = (int)$matches[1];
            $g = (int)$matches[2];
            $b = (int)$matches[3];
            
            // Check if it's very dark (close to black)
            if ($r < 50 && $g < 50 && $b < 50) {
                return false; // Very dark, use white text
            }
            
            // YIQ formula: brightness threshold lowered to 150
            $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
            return $yiq > 150; // Lower threshold to catch more dark colors
        }
        
        // Default to dark if we can't parse (safer to use white text)
        return false;
    }
    
    /**
     * Automatically set text color based on background brightness
     * Dark/black backgrounds get white text, light backgrounds get dark text
     */
    private function getTextColorForBackground(string $backgroundColor): string
    {
        return $this->isLightColor($backgroundColor) ? '#1f2937' : '#ffffff';
    }
    
    /**
     * Get available themes configuration
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
        
        // Automatically adjust text colors based on background brightness
        foreach ($themes as &$theme) {
            // Auto-adjust sender text color based on sender bubble brightness
            if (isset($theme['sender_bubble'])) {
                $theme['sender_text'] = $this->getTextColorForBackground($theme['sender_bubble']);
            }
            
            // Auto-adjust receiver text color based on receiver bubble brightness
            if (isset($theme['receiver_bubble'])) {
                $theme['receiver_text'] = $this->getTextColorForBackground($theme['receiver_bubble']);
            }
            
            // Auto-adjust header text color based on header background brightness
            if (isset($theme['header_color']) && !isset($theme['header_text_color'])) {
                $theme['header_text_color'] = $this->getTextColorForBackground($theme['header_color']);
            }
        }
        unset($theme); // Break reference
        
        return $themes;
    }

    /**
     * Get profile picture URL for user
     */
    private function getProfilePictureUrl(User $user): ?string
    {
        if ($user->profile_picture) {
            $media = \App\Models\MediaLibrary::find($user->profile_picture);
            if ($media) {
                return asset('storage/' . $media->file_path);
            }
        }

        // Fallback to UI Avatars
        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=64&background=055498&color=fff';
    }
}

