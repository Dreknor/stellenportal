<x-layouts.auth :title="__('Registrieren')">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Neue Organisation erstellen') }}</h1>
                <p class="mt-2 text-gray-600">{{ __('Erstellen Sie ein neues Konto, um Ihre Organisation zu verwalten.') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Sollte Ihr Träger bereits angemeldet sein, erhalten Sie von diesem die Zugangsdaten.') }}</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
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
                    <x-forms.input label="Email" name="email" type="email" placeholder="ihre@email.com" required/>
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

                @if(App::environment('production'))
                    <div>
                        {!! app('captcha')->display() !!}
                    </div>
                @endif

                <!-- Register Button -->
                <x-button type="primary" class="w-full py-3 text-lg font-semibold">{{ __('Account anlegen') }}</x-button>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-8 pt-6 border-t border-gray-200">
                <p class="text-gray-600">
                    {{ __('Sie haben bereits einen Zugang?') }}
                    <a href="{{ route('login') }}"
                        class="text-blue-600 hover:text-blue-700 hover:underline font-semibold ml-1">{{ __('Jetzt anmelden') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
