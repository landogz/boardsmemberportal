<?php

namespace App\Http\Controllers;

use App\Models\MediaLibrary;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * View a user's profile (admin or owner)
     */
    public function show($id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        // Allow admins or the owner of the profile
        if ($currentUser->privilege !== 'admin' && $currentUser->id !== $user->id) {
            abort(403);
        }

        return view('admin.profile-show', compact('user'));
    }

    /**
     * Show edit profile page
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Show admin edit profile page
     */
    public function adminEdit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'government_agency_id' => 'nullable|exists:government_agencies,id',
            'representative_type' => 'nullable|in:Board Member,Authorized Representative',
            'pre_nominal_title' => 'nullable|in:Mr.,Ms.',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'post_nominal_title' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'sex' => 'nullable|in:Male,Female',
            'gender' => 'nullable|in:Male,Female,Non-Binary',
            'birth_date' => 'nullable|date',
            'office_building_no' => 'nullable|string|max:255',
            'office_house_no' => 'nullable|string|max:255',
            'office_street_name' => 'nullable|string|max:255',
            'office_purok' => 'nullable|string|max:255',
            'office_sitio' => 'nullable|string|max:255',
            'office_region' => 'nullable|string|max:255',
            'office_province' => 'nullable|string|max:255',
            'office_city_municipality' => 'nullable|string|max:255',
            'office_barangay' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'mobile' => 'required|string|max:13|regex:/^\+63[0-9]{10}$/',
            'landline' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'representative_name' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'mobile.regex' => 'Mobile number must be in format +63 followed by 10 digits.',
        ]);

        $data = [
            'government_agency_id' => $request->government_agency_id,
            'representative_type' => $request->representative_type,
            'pre_nominal_title' => $request->pre_nominal_title,
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'post_nominal_title' => $request->post_nominal_title,
            'designation' => $request->designation,
            'sex' => $request->sex,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'office_building_no' => $request->office_building_no,
            'office_house_no' => $request->office_house_no,
            'office_street_name' => $request->office_street_name,
            'office_purok' => $request->office_purok,
            'office_sitio' => $request->office_sitio,
            'office_region' => $request->office_region,
            'office_province' => $request->office_province,
            'office_city_municipality' => $request->office_city_municipality,
            'office_barangay' => $request->office_barangay,
            'email' => $request->email,
            'username' => $request->username,
            'mobile' => $request->mobile,
            'landline' => $request->landline,
            'company' => $request->company,
            'position' => $request->position,
            'representative_name' => $request->representative_name,
        ];
        
        // Mark username as edited if it was changed
        if ($request->username && $request->username !== $user->username) {
            $data['username_edited'] = true;
        }

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

        AuditLogger::log(
            'profile.updated',
            'User updated profile information',
            $user,
            [
                'email' => $user->email,
                'username' => $user->username,
            ]
        );

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
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

        $user->update(['profile_picture' => $media->id]);

        $profilePicUrl = asset('storage/' . $media->file_path);

        AuditLogger::log(
            'profile.picture_updated',
            'User updated profile picture',
            $user,
            [
                'media_id' => $media->id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile picture uploaded successfully',
            'profile_picture_url' => $profilePicUrl,
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

        AuditLogger::log(
            'profile.password_updated',
            'User updated account password',
            $user
        );

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Remove profile picture
     */
    public function removeProfilePicture()
    {
        $user = Auth::user();

        if (!$user->profile_picture) {
            return response()->json([
                'success' => false,
                'message' => 'No profile picture to remove',
            ], 400);
        }

        // Delete old profile picture if exists
        $oldMedia = MediaLibrary::find($user->profile_picture);
        if ($oldMedia && Storage::disk('public')->exists($oldMedia->file_path)) {
            Storage::disk('public')->delete($oldMedia->file_path);
        }
        if ($oldMedia) {
            $oldMedia->delete();
        }

        $user->update(['profile_picture' => null]);

        AuditLogger::log(
            'profile.picture_removed',
            'User removed profile picture',
            $user
        );

        // Generate default avatar URL
        $defaultAvatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=200&background=055498&color=fff';

        return response()->json([
            'success' => true,
            'message' => 'Profile picture removed successfully',
            'profile_picture_url' => $defaultAvatarUrl,
        ]);
    }

    /**
     * Check if username is available
     */
    public function checkUsername(Request $request)
    {
        $user = Auth::user();
        $username = $request->input('username');

        if (empty($username)) {
            return response()->json([
                'available' => false,
                'message' => 'Username is required'
            ]);
        }

        // Check if username is taken by another user
        $exists = User::where('username', $username)
                     ->where('id', '!=', $user->id)
                     ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This username is already taken' : 'Username is available'
        ]);
    }
}

