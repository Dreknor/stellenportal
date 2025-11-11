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
                {!! RecaptchaV3::field('register') !!}

                <x-button type="primary" class="w-full py-3 text-lg font-semibold">{{ __('Anmelden') }}</x-button>
            </form>

            @if(config('services.keycloak.client_id') && config('services.keycloak.base_url'))
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">{{ __('oder') }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('keycloak.redirect') }}"
                           class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.5 0C5.149 0 0 5.149 0 11.5S5.149 23 11.5 23 23 17.851 23 11.5 17.851 0 11.5 0zm0 21.5C6.262 21.5 2 17.238 2 12S6.262 2.5 11.5 2.5 21 6.762 21 12s-4.262 9.5-9.5 9.5zm4.5-9.5c0 2.485-2.015 4.5-4.5 4.5S7 14.485 7 12s2.015-4.5 4.5-4.5S16 9.515 16 12z"/>
                            </svg>
                            {{ __('Mit Keycloak anmelden') }}
                        </a>
                    </div>
                </div>
            @endif

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
