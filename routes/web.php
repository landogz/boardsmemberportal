<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Check if coming soon mode is enabled
    if (config('app.coming_soon_enabled', false)) {
        return view('coming-soon');
    }
    
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
Route::get('/login', function (Request $request) {
    // If user is already logged in, redirect to the intended page or dashboard
    if (Auth::check()) {
        $redirect = $request->query('redirect');
        if ($redirect) {
            return redirect(urldecode($redirect));
        }
        // Default redirect based on privilege
        if (Auth::user()->privilege === 'admin' || Auth::user()->privilege === 'consec') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('landing');
    }
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email')->middleware('guest');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

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
        // Allow access if user has admin or consec role/privilege (dashboard is for admin-level users)
        $user = Auth::user();
        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('consec') &&
            !in_array($user->privilege, ['admin', 'consec'])
        ) {
            return redirect()->route('dashboard');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Admin Dashboard Customization (admin / consec)
    Route::prefix('admin/dashboard')->name('admin.dashboard.')->group(function () {
        Route::get('/customize', [\App\Http\Controllers\Admin\DashboardPreferenceController::class, 'edit'])
            ->name('customize');

        Route::get('/preferences', [\App\Http\Controllers\Admin\DashboardPreferenceController::class, 'show'])
            ->name('preferences.show');

        Route::post('/preferences', [\App\Http\Controllers\Admin\DashboardPreferenceController::class, 'store'])
            ->name('preferences.store');
    });
    
    // Admin Messages Route
    Route::get('/admin/messages', function () {
        return view('admin.messages');
    })->name('admin.messages');

    // Admin Calendar Route
    Route::get('/admin/calendar', function () {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasPermission('view calendar events')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.calendar');
    })->name('admin.calendar');


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
        Route::get('/browse', [\App\Http\Controllers\MediaLibraryController::class, 'browse'])->name('browse');
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
        Route::post('/{id}/mark-as-unread', [\App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('mark-as-unread');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    });

    // Admin Notifications
    Route::prefix('admin/notifications')->name('admin.notifications.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'adminIndex'])->name('index');
    });

    // Board Resolutions (admin)
    Route::prefix('admin/board-resolutions')->name('admin.board-resolutions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'update'])->name('update');
        Route::get('/{id}/history', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'history'])->name('history');
        Route::get('/{id}/pdf', [\App\Http\Controllers\Admin\BoardResolutionController::class, 'servePdf'])->name('pdf');
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
        Route::get('/{id}/pdf', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'servePdf'])->name('pdf');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BoardRegulationController::class, 'destroy'])->name('destroy');
    });

    // Referendums (admin)
    Route::prefix('admin/referendums')->name('admin.referendums.')->group(function () {
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\ReferendumController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/', [\App\Http\Controllers\Admin\ReferendumController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ReferendumController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\ReferendumController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\ReferendumController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\ReferendumController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\Admin\ReferendumController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ReferendumController::class, 'destroy'])->name('destroy');
    });

    // Announcements (admin)
    Route::prefix('admin/notices')->name('admin.notices.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NoticeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NoticeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NoticeController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\NoticeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\NoticeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\NoticeController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\NoticeController::class, 'destroy'])->name('destroy');
    });

    // Reference Materials (admin)
    Route::prefix('admin/reference-materials')->name('admin.reference-materials.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReferenceMaterialController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\ReferenceMaterialController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\ReferenceMaterialController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\ReferenceMaterialController::class, 'reject'])->name('reject');
    });

    // Agenda Inclusion Requests (admin)
    Route::prefix('admin/agenda-inclusion-requests')->name('admin.agenda-inclusion-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AgendaInclusionRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AgendaInclusionRequestController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\AgendaInclusionRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\AgendaInclusionRequestController::class, 'reject'])->name('reject');
    });

    // Attendance Confirmations (admin)
    Route::prefix('admin/attendance-confirmations')->name('admin.attendance-confirmations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AttendanceConfirmationController::class, 'index'])->name('index');
        Route::post('/{id}/re-invite', [\App\Http\Controllers\Admin\AttendanceConfirmationController::class, 'reInvite'])->name('re-invite');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AttendanceConfirmationController::class, 'show'])->name('show');
    });

    Route::prefix('admin/announcements')->name('admin.announcements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('destroy');
    });

    // Report Generation
    Route::prefix('admin/report-generation')->name('admin.report-generation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportGenerationController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\Admin\ReportGenerationController::class, 'search'])->name('search');
    });

    // Announcements (authenticated users)
    Route::prefix('announcements')->name('announcements.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\AnnouncementController::class, 'show'])->name('show');
        Route::get('/api/landing', [\App\Http\Controllers\AnnouncementController::class, 'getForLanding'])->name('api.landing');
        Route::get('/api/{id}/modal', [\App\Http\Controllers\AnnouncementController::class, 'getForModal'])->name('api.modal');
    });

    // Calendar Events API
    Route::get('/api/calendar/events', [\App\Http\Controllers\CalendarController::class, 'getEvents'])->name('api.calendar.events')->middleware('auth');

    // Referendum Voting and Comments (authenticated users)
    Route::prefix('referendums')->name('referendums.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReferendumController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\ReferendumController::class, 'show'])->name('show');
        Route::get('/{id}/comments', [\App\Http\Controllers\ReferendumController::class, 'getComments'])->name('comments.get');
        Route::get('/{id}/comments/new', [\App\Http\Controllers\ReferendumController::class, 'getNewComments'])->name('comments.new');
        
        Route::post('/{id}/vote', [\App\Http\Controllers\ReferendumVoteController::class, 'store'])->name('vote');
        Route::get('/{id}/vote/statistics', [\App\Http\Controllers\ReferendumVoteController::class, 'statistics'])->name('vote.statistics');
        Route::post('/{id}/comments', [\App\Http\Controllers\ReferendumCommentController::class, 'store'])->name('comments.store');
        Route::post('/{id}/comments/{commentId}', [\App\Http\Controllers\ReferendumCommentController::class, 'update'])->name('comments.update');
        Route::delete('/{id}/comments/{commentId}', [\App\Http\Controllers\ReferendumCommentController::class, 'destroy'])->name('comments.destroy');
    });

    // Notices (authenticated users)
    Route::prefix('notices')->name('notices.')->middleware('auth')->group(function () {
        Route::get('/pending', [\App\Http\Controllers\NoticeController::class, 'getPendingNotices'])->name('pending');
        Route::get('/', [\App\Http\Controllers\NoticeController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\NoticeController::class, 'show'])->name('show');
        Route::post('/{id}/accept', [\App\Http\Controllers\NoticeController::class, 'accept'])->name('accept');
        Route::post('/{id}/decline', [\App\Http\Controllers\NoticeController::class, 'decline'])->name('decline');
        Route::post('/{id}/agenda-inclusion', [\App\Http\Controllers\NoticeController::class, 'submitAgendaInclusion'])->name('agenda-inclusion');
        Route::post('/{id}/reference-materials', [\App\Http\Controllers\NoticeController::class, 'submitReferenceMaterial'])->name('reference-materials');
    });

    // Profile Routes
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/view/{id}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-picture', [\App\Http\Controllers\ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
    Route::post('/profile/remove-picture', [\App\Http\Controllers\ProfileController::class, 'removeProfilePicture'])->name('profile.remove-picture');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/check-username', [\App\Http\Controllers\ProfileController::class, 'checkUsername'])->name('profile.check-username');

    // Admin Profile Routes
    Route::prefix('admin/profile')->name('admin.profile.')->middleware('auth')->group(function () {
        Route::get('/edit', [\App\Http\Controllers\ProfileController::class, 'adminEdit'])->name('edit');
    });

    // Board Issuances (only for authenticated users)
    Route::get('/board-issuances', [\App\Http\Controllers\BoardIssuanceController::class, 'index'])->name('board-issuances');

    // Notifications Route - handled by controller above

    // Messages Routes
    Route::get('/messages', function () {
        return view('messages');
    })->name('messages');
    
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::post('/{id}/react', [\App\Http\Controllers\MessageController::class, 'react'])->name('react');
        Route::get('/{id}/reactions', [\App\Http\Controllers\MessageController::class, 'getReactions'])->name('reactions');
        Route::post('/reactions/batch', [\App\Http\Controllers\MessageController::class, 'getBatchReactions'])->name('reactions.batch');
        Route::post('/{id}/reply', [\App\Http\Controllers\MessageController::class, 'reply'])->name('reply');
        Route::delete('/{id}', [\App\Http\Controllers\MessageController::class, 'delete'])->name('delete');
        
        // Group chat routes
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GroupChatController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\GroupChatController::class, 'create'])->name('create');
            Route::get('/themes', [\App\Http\Controllers\GroupChatController::class, 'getThemes'])->name('themes');
            Route::get('/{groupId}', [\App\Http\Controllers\GroupChatController::class, 'show'])->name('show');
            Route::put('/{groupId}', [\App\Http\Controllers\GroupChatController::class, 'update'])->name('update');
            Route::delete('/{groupId}', [\App\Http\Controllers\GroupChatController::class, 'destroy'])->name('destroy');
            Route::post('/{groupId}/members', [\App\Http\Controllers\GroupChatController::class, 'addMembers'])->name('members.add');
            Route::delete('/{groupId}/members', [\App\Http\Controllers\GroupChatController::class, 'removeMembers'])->name('members.remove');
            Route::post('/{groupId}/admins', [\App\Http\Controllers\GroupChatController::class, 'assignAdmin'])->name('admins.assign');
            Route::delete('/{groupId}/admins', [\App\Http\Controllers\GroupChatController::class, 'revokeAdmin'])->name('admins.revoke');
            Route::post('/{groupId}/theme', [\App\Http\Controllers\GroupChatController::class, 'applyTheme'])->name('theme.apply');
            Route::post('/{groupId}/leave', [\App\Http\Controllers\GroupChatController::class, 'leave'])->name('leave');
        });
        Route::get('/users', [\App\Http\Controllers\MessageController::class, 'getUsers'])->name('users');
        Route::post('/send', [\App\Http\Controllers\MessageController::class, 'sendMessage'])->name('send');
        Route::get('/conversation/{userId}', [\App\Http\Controllers\MessageController::class, 'getConversation'])->name('conversation');
        Route::post('/decrypt', [\App\Http\Controllers\MessageController::class, 'decryptConversation'])->name('decrypt');
        Route::get('/conversations', [\App\Http\Controllers\MessageController::class, 'getConversations'])->name('conversations');
        Route::get('/recent', [\App\Http\Controllers\MessageController::class, 'getConversations'])->name('recent'); // Alias for dropdown
        Route::get('/new/{userId}', [\App\Http\Controllers\MessageController::class, 'getNewMessages'])->name('new');
        Route::post('/{userId}/mark-as-read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('mark-as-read');
        Route::get('/unread-count', [\App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('unread-count');
        
        // Single chat theme routes
        Route::get('/themes', [\App\Http\Controllers\MessageController::class, 'getThemes'])->name('themes');
        Route::get('/conversation/{otherUserId}/theme', [\App\Http\Controllers\MessageController::class, 'getConversationTheme'])->name('conversation.theme');
        Route::post('/conversation/{otherUserId}/theme', [\App\Http\Controllers\MessageController::class, 'applyConversationTheme'])->name('conversation.theme.apply');
    });
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
