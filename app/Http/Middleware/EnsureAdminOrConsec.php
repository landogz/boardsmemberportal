<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrConsec
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has admin or consec privilege
        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('consec') &&
            !in_array($user->privilege, ['admin', 'consec'])
        ) {
            // Redirect to landing page if user doesn't have admin or consec privilege
            return redirect()->route('landing');
        }

        return $next($request);
    }
}
