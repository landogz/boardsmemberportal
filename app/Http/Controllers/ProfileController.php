<?php

namespace App\Http\Controllers;

use App\Models\MediaLibrary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show edit profile page
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'representative_name' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'company' => $request->company,
            'position' => $request->position,
            'representative_name' => $request->representative_name,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                $oldMedia = MediaLibrary::find($user->profile_picture);
                if ($oldMedia && Storage::disk('public')->exists($oldMedia->file_path)) {
                    Storage::disk('public')->delete($oldMedia->file_path);
                }
                if ($oldMedia) {
                    $oldMedia->delete();
                }
            }

            // Upload new profile picture
            $file = $request->file('profile_picture');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'profile-pictures/' . $fileName;
            
            Storage::disk('public')->put($filePath, file_get_contents($file));

            // Create media library entry
            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_path' => $filePath,
                'uploaded_by' => $user->id,
            ]);

            $data['profile_picture'] = $media->id;
        }

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old banner image if exists
            if ($user->banner_image) {
                $oldMedia = MediaLibrary::find($user->banner_image);
                if ($oldMedia && Storage::disk('public')->exists($oldMedia->file_path)) {
                    Storage::disk('public')->delete($oldMedia->file_path);
                }
                if ($oldMedia) {
                    $oldMedia->delete();
                }
            }

            // Upload new banner image
            $file = $request->file('banner_image');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'banner-images/' . $fileName;
            
            Storage::disk('public')->put($filePath, file_get_contents($file));

            // Create media library entry
            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_path' => $filePath,
                'uploaded_by' => $user->id,
            ]);

            $data['banner_image'] = $media->id;
        }

        $user->update($data);

        $profilePicUrl = $user->profile_picture ? asset('storage/' . MediaLibrary::find($user->profile_picture)->file_path) : null;
        $bannerPicUrl = $user->banner_image ? asset('storage/' . MediaLibrary::find($user->banner_image)->file_path) : null;

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'profile_picture_url' => $profilePicUrl,
                'banner_image_url' => $bannerPicUrl,
            ],
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password_hash' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}

