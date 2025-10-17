<x-layouts.auth :title="__('Anmelden')">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 sm:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Willkommen zurück') }}</h1>
                <p class="mt-2 text-gray-600">{{ __('Melden Sie sich bei Ihrem Konto an') }}</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <x-forms.input label="E-Mail" name="email" type="email" placeholder="ihre@email.com" autofocus />
                </div>

                <div>
                    <x-forms.input label="Passwort" name="password" type="password" placeholder="••••••••" />

                    <div class="flex items-center justify-between mt-3">
                        <x-forms.checkbox label="Angemeldet bleiben" name="remember" />
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm text-blue-600 hover:text-blue-700 hover:underline font-medium">{{ __('Passwort vergessen?') }}</a>
                        @endif
                    </div>
                </div>

                <x-button type="primary" class="w-full py-3 text-lg font-semibold">{{ __('Anmelden') }}</x-button>
            </form>

            @if (Route::has('register'))
                <div class="text-center mt-8 pt-6 border-t border-gray-200">
                    <p class="text-gray-600">
                        {{ __('Sie haben noch kein Konto?') }}
                        <a href="{{ route('register') }}"
                           class="text-blue-600 hover:text-blue-700 hover:underline font-semibold ml-1">{{ __('Jetzt registrieren') }}</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.auth>
