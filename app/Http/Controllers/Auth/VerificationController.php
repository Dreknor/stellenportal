<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        // Find the user by ID
        $user = \App\Models\User::findOrFail($id);

        // Verify the hash matches
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // If user is already verified, redirect to login or dashboard
        if ($user->hasVerifiedEmail()) {
            if (auth()->check()) {
                return redirect()->route('dashboard')->with('status', 'Email already verified.');
            }
            return redirect()->route('login')->with('status', 'Email bereits bestätigt. Bitte melden Sie sich an.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Log the user in automatically after verification
        auth()->login($user);

        return redirect()->route('dashboard')->with('status', 'E-Mail erfolgreich bestätigt!');
    }
}
