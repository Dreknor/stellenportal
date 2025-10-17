<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Ensure static analyzers know $user is an instance of App\Models\User before accessing custom properties
        if ($user instanceof \App\Models\User && $user->change_password && ! $request->session()->has('ownID')) {
            return redirect()->route('settings.password.edit');
        }

        return $next($request);
    }
}
