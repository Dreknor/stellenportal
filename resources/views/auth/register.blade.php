<x-layouts.auth>
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-3">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Neue Organisation erstellen') }}</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('Erstellen Sie ein neues Konto, um Ihre Organisation zu verwalten.') }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __(' Sollte Ihr Träger bereits angemeldet sein, erhalten Sie von diesem die Zugangsdaten.') }}</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf
                <!-- Full Name Input -->
                <div>
                    <x-forms.input label="Vorname" name="first_name" type="text" placeholder="{{ __('Vorname') }}" autofocus required />
                </div>
                <!-- Full Name Input -->
                <div>
                    <x-forms.input label="Nachname" name="last_name" type="text" placeholder="{{ __('Nachname') }}" required />
                </div>

                <!-- Email Input -->
                <div>
                    <x-forms.input label="Email" name="email" type="email" placeholder="your@email.com" required/>
                </div>

                <!-- Password Input -->
                <div>
                    <x-forms.input label="Passwort" name="password" type="password" placeholder="••••••••" required/>
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <x-forms.input label="Passwort bestätigen" name="password_confirmation" type="password" required
                        placeholder="••••••••" />
                </div>

                <!-- Register Button -->
                <x-button type="primary" class="w-full">{{ __('Account anlegen') }}</x-button>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Sie haben bereits einen Zugang?') }}
                    <a href="{{ route('login') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Anmelden') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
