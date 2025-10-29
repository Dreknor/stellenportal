<x-layouts.auth :title="__('Passwort vergessen')">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 sm:p-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Passwort vergessen') }}</h1>
                <p class="text-gray-600 mt-2">
                    {{ __('Geben Sie Ihre E-Mail-Adresse ein, um einen Link zum Zurücksetzen des Passworts zu erhalten') }}</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200">
                    <p class="text-sm text-green-800 font-medium">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <x-forms.input name="email" type="email" label="E-Mail" placeholder="ihre@email.com" />
                </div>
                {!! RecaptchaV3::field('register') !!}

                <x-button type="primary" buttonType="submit" class="w-full py-3 text-lg font-semibold">
                    {{ __('Link zum Zurücksetzen des Passworts senden') }}
                </x-button>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('login') }}"
                   class="text-blue-600 hover:text-blue-700 hover:underline font-semibold">{{ __('Zurück zur Anmeldung') }}</a>
            </div>
        </div>
    </div>
</x-layouts.auth>
