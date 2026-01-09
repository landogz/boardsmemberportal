<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        if (config('app.maintenance_mode_enabled', false)) {
            $path = $request->path();
            
            // Allow access to:
            // - Root path (where maintenance page is shown)
            // - API routes (for any API calls)
            // - Health check route
            if ($path === '/' || 
                $request->is('api/*') || 
                $request->is('up') ||
                $request->is('health')) {
                return $next($request);
            }
            
            // Redirect all other pages to maintenance
            return redirect('/');
        }

        return $next($request);
    }
}

