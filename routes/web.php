<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->privilege === 'admin' || Auth::user()->privilege === 'consec') {
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

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email')->middleware('guest');

Route::get('/api/government-agencies', function () {
    $agencies = \App\Models\GovernmentAgency::active()->with('logo')->orderBy('name')->get(['id', 'name', 'code', 'logo_id']);
    return response()->json($agencies->map(function($agency) {
        return [
            'id' => $agency->id,
            'name' => $agency->name,
            'code' => $agency->code,
            'logo_url' => $agency->logo ? asset('storage/' . $agency->logo->file_path) : null
        ];
    }));
})->name('api.government-agencies');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Activity tracking endpoint
Route::post('/api/track-activity', function() {
    if (Auth::check()) {
        $user = Auth::user();
        $wasOffline = !$user->is_online;
        $user->last_activity = now();
        if (!$user->is_online) {
            $user->is_online = true;
        }
        $user->save();
        
        // Log activity update if user came back online (but not on every ping to avoid spam)
        // Only log if user was previously offline
        if ($wasOffline) {
            \App\Services\AuditLogger::log(
                'auth.activity_resumed',
                'User activity resumed - marked as online',
                $user,
                [
                    'session_id' => $user->current_session_id,
                    'ip_address' => request()->ip(),
                ]
            );
        }
        
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 401);
})->middleware('auth')->name('api.track-activity');

// Dashboard Routes (Protected)
Route::middleware(['auth', 'track.activity'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('landing');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        // Allow access if user has admin role (which has all permissions) or has any admin permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && 
            !$user->hasPermission('view board resolutions') && 
            !$user->hasPermission('view board regulations') && 
            !$user->hasPermission('view government agencies') && 
            !$user->hasPermission('view media library') && 
            !$user->hasPermission('view audit logs') && 
            !$user->hasPermission('view roles') && 
            !$user->hasPermission('view permissions') && 
            !$user->hasPermission('manage consec accounts')) {
            return redirect()->route('dashboard');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Government Agency Routes
    Route::prefix('admin/government-agencies')->name('admin.government-agencies.')->group(function () {
        Route::get('/', [\App\Http\Controllers\GovernmentAgencyController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\GovernmentAgencyController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\GovernmentAgencyController::class, 'store'])->name('store');
        Route::delete('/bulk-delete', [\App\Http\Controllers\GovernmentAgencyController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{id}/edit', [\App\Http\Controllers\GovernmentAgencyController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\GovernmentAgencyController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\GovernmentAgencyController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\GovernmentAgencyController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/settings', [\App\Http\Controllers\GovernmentAgencyController::class, 'settings'])->name('settings');
        Route::post('/settings/save', [\App\Http\Controllers\GovernmentAgencyController::class, 'saveSettings'])->name('settings.save');
        Route::post('/bulk/activate', [\App\Http\Controllers\GovernmentAgencyController::class, 'bulkActivate'])->name('bulk.activate');
        Route::post('/bulk/deactivate', [\App\Http\Controllers\GovernmentAgencyController::class, 'bulkDeactivate'])->name('bulk.deactivate');
    });

    // Media Library Routes
    Route::prefix('admin/media-library')->name('admin.media-library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MediaLibraryController::class, 'index'])->name('index');
        Route::post('/upload', [\App\Http\Controllers\MediaLibraryController::class, 'store'])->name('store');
        Route::get('/{id}/download', [\App\Http\Controllers\MediaLibraryController::class, 'download'])->name('download');
        Route::get('/{id}', [\App\Http\Controllers\MediaLibraryController::class, 'show'])->name('show');
        Route::post('/{id}/update', [\App\Http\Controllers\MediaLibraryController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\MediaLibraryController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [\App\Http\Controllers\MediaLibraryController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Audit Logs (admin)
    Route::get('/admin/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit-logs.index');
    Route::get('/admin/audit-logs/export-pdf', [\App\Http\Controllers\AuditLogController::class, 'exportPdf'])->name('admin.audit-logs.export-pdf');

    // Roles and Permissions (admin)
    Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/update-permission', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('update-permission');
    });

    Route::prefix('admin/permissions')->name('admin.permissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PermissionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('destroy');
    });

    // CONSEC Account Management (admin)
    Route::prefix('admin/consec')->name('admin.consec.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CONSECController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CONSECController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\CONSECController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CONSECController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\CONSECController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CONSECController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\CONSECController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{id}/permissions', [\App\Http\Controllers\Admin\CONSECController::class, 'getPermissions'])->name('permissions');
        Route::post('/{id}/permissions', [\App\Http\Controllers\Admin\CONSECController::class, 'updatePermissions'])->name('update-permissions');
    });

    // Board Member Management (admin)
    Route::prefix('admin/board-members')->name('admin.board-members.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BoardMemberController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BoardMemberController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\BoardMemberController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BoardMemberController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\BoardMemberController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BoardMemberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\BoardMemberController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{id}/permissions', [\App\Http\Controllers\Admin\BoardMemberController::class, 'getPermissions'])->name('permissions');
        Route::post('/{id}/permissions', [\App\Http\Controllers\Admin\BoardMemberController::class, 'updatePermissions'])->name('update-permissions');
    });

    // Pending Registrations Management (admin)
    Route::prefix('admin/pending-registrations')->name('admin.pending-registrations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PendingRegistrationsController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PendingRegistrationsController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\PendingRegistrationsController::class, 'approve'])->name('approve');
        Route::post('/{id}/disapprove', [\App\Http\Controllers\Admin\PendingRegistrationsController::class, 'disapprove'])->name('disapprove');
    });

    // Notifications (authenticated users)
    Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('recent');
        Route::post('/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Admin Notifications
    Route::prefix('admin/notifications')->name('admin.notifications.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    });

    // Board Resolutions (admin)
    Route::prefix('admin/board-resolutions')->name('admin.board-resolutions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'update'])->name('update');
        Route::get('/{id}/history', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'history'])->name('history');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'destroy'])->name('destroy');
    });

    // Board Regulations (admin)
    Route::prefix('admin/board-regulations')->name('admin.board-regulations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'update'])->name('update');
        Route::get('/{id}/history', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'history'])->name('history');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'destroy'])->name('destroy');
    });

    // Profile Routes
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/view/{id}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-picture', [\App\Http\Controllers\ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/check-username', [\App\Http\Controllers\ProfileController::class, 'checkUsername'])->name('profile.check-username');

    // Admin Profile Routes
    Route::prefix('admin/profile')->name('admin.profile.')->middleware('auth')->group(function () {
        Route::get('/edit', [\App\Http\Controllers\ProfileController::class, 'adminEdit'])->name('edit');
    });

    // Board Issuances (only for authenticated users)
    Route::get('/board-issuances', [\App\Http\Controllers\BoardIssuanceController::class, 'index'])->name('board-issuances');

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

// Temporary route to check PHP configuration
// DELETE THIS ROUTE AFTER CHECKING PHP SETTINGS
Route::get('/phpinfo', function () {
    if (Auth::check() && Auth::user()->privilege === 'admin') {
        phpinfo();
    } else {
        return redirect()->route('login');
    }
})->name('phpinfo');
