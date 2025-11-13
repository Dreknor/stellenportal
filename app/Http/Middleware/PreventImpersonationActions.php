<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventImpersonationActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if currently impersonating
        if (session()->has('impersonate_original_user')) {
            // Prevent certain actions while impersonating
            $restrictedRoutes = [
                'profile.password',
                'profile.delete',
                'admin.*', // Prevent all admin actions while impersonating
            ];

            foreach ($restrictedRoutes as $route) {
                if ($request->routeIs($route)) {
                    return redirect()
                        ->back()
                        ->with('error', 'Diese Aktion kann während der Impersonierung nicht durchgeführt werden.');
                }
            }
        }

        return $next($request);
    }
}

