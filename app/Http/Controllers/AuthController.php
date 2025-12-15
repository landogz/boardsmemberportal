<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GovernmentAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if ($user->status !== 'approved') {
            throw ValidationException::withMessages([
                'email' => ['Your account is pending approval.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'privilege' => $user->privilege,
            ],
            'redirect' => $user->privilege === 'admin' ? route('admin.dashboard') : route('landing'),
        ]);
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        // Custom password validation
        $request->validate([
            'government_agency_id' => 'required|exists:government_agencies,id',
            'pre_nominal_title' => 'required|in:Mr.,Ms.',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'post_nominal_title' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'gender' => 'required|in:Male,Female,Non-Binary',
            'birth_date' => 'required|date|before:today',
            'office_region' => 'required|string|max:255',
            'office_province' => 'required|string|max:255',
            'office_city_municipality' => 'required|string|max:255',
            'office_barangay' => 'required|string|max:255',
            'office_building_no' => 'nullable|string|max:255',
            'office_house_no' => 'nullable|string|max:255',
            'office_street_name' => 'nullable|string|max:255',
            'office_purok' => 'nullable|string|max:255',
            'office_sitio' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'mobile' => 'required|string|max:20|regex:/^\+63[0-9]{10}$/',
            'landline' => 'nullable|string|max:50',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('The password must contain at least one capital letter.');
                    }
                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('The password must contain at least one small letter.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('The password must contain at least one number.');
                    }
                    if (!preg_match('/[~!@#$%^&*|]/', $value)) {
                        $fail('The password must contain at least one special character (~, !, #, $, %, ^, &, *, |, etc.).');
                    }
                },
            ],
        ], [
            'mobile.regex' => 'Mobile number must be in format +63 followed by 10 digits.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Generate username if not provided or ensure uniqueness
        $username = $request->username;
        $originalUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'id' => Str::uuid(),
            'government_agency_id' => $request->government_agency_id,
            'pre_nominal_title' => $request->pre_nominal_title,
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'post_nominal_title' => $request->post_nominal_title,
            'designation' => $request->designation,
            'sex' => $request->sex,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'office_region' => $request->office_region,
            'office_province' => $request->office_province,
            'office_city_municipality' => $request->office_city_municipality,
            'office_barangay' => $request->office_barangay,
            'office_building_no' => $request->office_building_no,
            'office_house_no' => $request->office_house_no,
            'office_street_name' => $request->office_street_name,
            'office_purok' => $request->office_purok,
            'office_sitio' => $request->office_sitio,
            'email' => $request->email,
            'username' => $username,
            'username_edited' => false,
            'password_hash' => Hash::make($request->password),
            'privilege' => 'user',
            'is_active' => true,
            'mobile' => $request->mobile,
            'landline' => $request->landline,
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Your account is pending approval by CONSEC.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'username' => $user->username,
            ],
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'redirect' => '/',
        ]);
    }
}

