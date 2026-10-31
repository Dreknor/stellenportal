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
        try {
            // Explicitly set the redirect URI
            return Socialite::driver('keycloak')
                ->redirectUrl(config('services.keycloak.redirect'))
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Keycloak Redirect Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect('/')
                ->with('error', 'Fehler beim Weiterleiten zu Keycloak. Bitte versuchen Sie es später erneut.');
        }
    }

    /**
     * Handle the callback from Keycloak
     */
    public function callback(): RedirectResponse
    {
        try {
            $keycloakUser = Socialite::driver('keycloak')->user();

            $keycloakId = (string) $keycloakUser->getId();

            if ($keycloakId === '') {
                return redirect('/')
                    ->with('error', 'Fehler bei der Anmeldung über Keycloak. Bitte versuchen Sie es erneut.');
            }

            // Already linked accounts are matched by the Keycloak subject only
            $user = User::where('keycloak_id', $keycloakId)->first();

            if (!$user) {
                // First login: link by email, but only with an email address verified by Keycloak
                $emailVerified = filter_var($keycloakUser->user['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $user = $emailVerified
                    ? User::where('email', $keycloakUser->getEmail())->whereNull('keycloak_id')->first()
                    : null;

                if (!$user) {
                    return redirect('/')
                        ->with('error', 'Kein passendes Benutzerkonto gefunden oder E-Mail-Adresse in Keycloak nicht bestätigt. Bitte kontaktieren Sie den Administrator.');
                }

                $user->keycloak_id = $keycloakId;
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

