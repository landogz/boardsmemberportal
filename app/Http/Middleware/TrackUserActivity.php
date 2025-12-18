<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = session()->getId();
            
            // Check if the current session matches the user's active session
            if ($user->current_session_id && $user->current_session_id !== $currentSessionId) {
                // Log the session invalidation before logging out
                AuditLogger::log(
                    'auth.session_invalidated',
                    'Session invalidated - user logged in on another device',
                    $user,
                    [
                        'invalid_session_id' => $currentSessionId,
                        'active_session_id' => $user->current_session_id,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url' => $request->fullUrl(),
                    ]
                );
                
                // Set user as offline
                $user->is_online = false;
                $user->current_session_id = null;
                $user->save();
                
                // User is logged in on another device, log them out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->with('error', 'Your account has been logged in on another device. You have been logged out.');
            }
            
            // Update last activity timestamp
            $user->last_activity = now();
            
            // Set user as online if not already
            if (!$user->is_online) {
                $user->is_online = true;
            }
            
            // Update current session ID if not set
            if (!$user->current_session_id) {
                $user->current_session_id = $currentSessionId;
            }
            
            $user->save();
        }

        return $next($request);
    }
}
