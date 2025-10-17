<x-layouts.auth :title="__('Anmelden')">
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-3">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Melden Sie sich bei Ihrem Konto an') }}</h1>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <div>
                    <x-forms.input label="E-Mail" name="email" type="email" placeholder="ihre@email.com" autofocus />
                </div>

                <div>
                    <x-forms.input label="Passwort" name="password" type="password" placeholder="••••••••" />

                    <div class="flex items-center justify-between mt-2">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ __('Passwort vergessen?') }}</a>
                        @endif
                        <x-forms.checkbox label="Angemeldet bleiben" name="remember" />
                    </div>
                </div>

                <x-button type="primary" class="w-full">{{ __('Anmelden') }}</x-button>
            </form>

            @if (Route::has('register'))
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Sie haben noch kein Konto?') }}
                        <a href="{{ route('register') }}"
                           class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Registrieren') }}</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.auth>
