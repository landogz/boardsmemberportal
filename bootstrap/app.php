<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (needed for AlwaysData hosting)
        $middleware->trustProxies(at: '*');
        
        // Add coming soon middleware globally
        $middleware->append(\App\Http\Middleware\ComingSoonMiddleware::class);
        
        $middleware->alias([
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'prevent.user.admin' => \App\Http\Middleware\PreventUserAccessToAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // On CSRF token mismatch (419): logout, clear session, then redirect or return JSON
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session has expired or you were logged in on another device. Please log in again.',
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()->route('login')->with('error', 'Your session has expired or you were logged in on another device. Please log in again.');
        });
    })->create();
