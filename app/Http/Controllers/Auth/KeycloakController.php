<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class KeycloakController extends Controller
{
    /**
     * Redirect to Keycloak for authentication
     */
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * Handle the callback from Keycloak
     */
    public function callback(): RedirectResponse
    {
        try {
            $keycloakUser = Socialite::driver('keycloak')->user();

            // Try to find an existing user by email
            $user = User::where('email', $keycloakUser->getEmail())->first();

            // If no user exists, redirect to home page with message
            if (!$user) {
                return redirect('/')
                    ->with('error', 'Kein Benutzerkonto für diese E-Mail-Adresse gefunden. Bitte kontaktieren Sie den Administrator.');
            }

            // Update keycloak_id if not already set
            if (empty($user->keycloak_id) && !empty($keycloakUser->getId())) {
                $user->keycloak_id = $keycloakUser->getId();
                $user->save();
            }

            // Log the user in
            Auth::login($user);

            // Regenerate session to prevent fixation attacks
            request()->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            Log::error('Keycloak SSO Error: ' . $e->getMessage());

            return redirect('/')
                ->with('error', 'Fehler bei der Anmeldung über Keycloak. Bitte versuchen Sie es erneut.');
        }
    }
}

