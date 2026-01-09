<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if coming soon mode is enabled
        if (config('app.coming_soon_enabled', false)) {
            // Allow access to the coming soon page itself and API routes
            $path = $request->path();
            
            // Don't redirect if already on coming soon page or if it's an API route
            if ($path !== '/' && !$request->is('api/*')) {
                // Redirect to coming soon page
                return redirect('/');
            }
        }

        return $next($request);
    }
}
