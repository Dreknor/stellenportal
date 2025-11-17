<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Start impersonating a user
     */
    public function start(Request $request, User $user)
    {
        // Check permission
        if (!Auth::user()->can('admin impersonate users')) {
            abort(403, 'Keine Berechtigung zum Impersonieren von Benutzern.');
        }

        // Cannot impersonate yourself
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Sie können sich nicht selbst impersonieren.');
        }

        // Cannot impersonate another admin (with same or higher permissions)
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->back()->with('error', 'Sie können keine Admin-Benutzer impersonieren.');
        }

        // Store original user ID in session
        session(['impersonate_original_user' => Auth::id()]);

        // Login as the target user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Sie sind jetzt als {$user->name} angemeldet.");
    }

    /**
     * Stop impersonating and return to original user
     */
    public function stop(Request $request)
    {
        // Check if currently impersonating
        if (!session()->has('impersonate_original_user')) {
            return redirect()->route('dashboard')->with('error', 'Sie impersonieren derzeit keinen Benutzer.');
        }

        $originalUserId = session('impersonate_original_user');
        $impersonatedUser = Auth::user();

        // Get original user
        $originalUser = User::findOrFail($originalUserId);

        // Remove impersonation session
        session()->forget('impersonate_original_user');

        // Login back as original user
        Auth::login($originalUser);

        return redirect()->route('admin.dashboard')->with('success', 'Impersonierung beendet. Sie sind wieder als ' . $originalUser->name . ' angemeldet.');
    }

    /**
     * Check if user is currently impersonating
     */
    public static function isImpersonating(): bool
    {
        return session()->has('impersonate_original_user');
    }

    /**
     * Get the original user if impersonating
     */
    public static function getOriginalUser(): ?User
    {
        if (!self::isImpersonating()) {
            return null;
        }

        return User::find(session('impersonate_original_user'));
    }
}

