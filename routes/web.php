<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->privilege === 'admin') {
        return redirect()->route('admin.dashboard');
        }
        return view('landing');
    }
    return view('landing');
})->name('landing');

Route::get('/2', function () {
    return view('landing2');
});

Route::get('/example', function () {
    return view('example');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::get('/api/government-agencies', function () {
    $agencies = \App\Models\GovernmentAgency::active()->orderBy('name')->get(['id', 'name', 'code']);
    return response()->json($agencies);
})->name('api.government-agencies');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('landing');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        if (Auth::user()->privilege !== 'admin') {
            return redirect()->route('dashboard');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/portal-manager', function () {
        if (Auth::user()->privilege !== 'admin') {
            return redirect()->route('dashboard');
        }
        return view('admin.portal-manager');
    })->name('admin.portal-manager');

    Route::post('/admin/portal-manager/send-email', function () {
        if (Auth::user()->privilege !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        // Validate request
        $validated = request()->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'in:board_members,authorized_representatives,consec',
            'email_subject' => 'required|string|max:255',
            'email_content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:25600', // 25MB max
            'remarks' => 'nullable|string',
        ]);

        // TODO: Implement actual email sending functionality
        // For now, return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully to selected recipients.'
        ]);
    })->name('admin.portal-manager.send-email');

    // Profile Routes
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications Route
    Route::get('/notifications', function () {
        return view('notifications');
    })->name('notifications');

    // Messages Route
    Route::get('/messages', function () {
        return view('messages');
    })->name('messages');
});

Route::post('/api/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Data received successfully',
        'data' => request()->all()
    ]);
});
