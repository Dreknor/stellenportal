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
                'settings.password.update',
                'settings.profile.update',
                'settings.profile.destroy',
                'admin.*', // Prevent all admin actions while impersonating
                'cms.*',
            ];

            foreach ($restrictedRoutes as $route) {
                if ($request->routeIs($route) && !$request->routeIs('admin.impersonate.stop')) {
                    return redirect()
                        ->back()
                        ->with('error', 'Diese Aktion kann während der Impersonierung nicht durchgeführt werden.');
                }
            }
        }

        return $next($request);
    }
}

