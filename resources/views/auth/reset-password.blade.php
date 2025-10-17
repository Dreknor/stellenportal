<x-layouts.auth :title="__('Passwort zurücksetzen')">
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Passwort zurücksetzen') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Geben Sie Ihre E-Mail und Ihr neues Passwort unten ein.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div class="mb-4">
                    <x-forms.input name="email" type="email" label="E-Mail"
                                   value="{{ old('email', request('email')) }}" placeholder="ihre@email.com" />
                </div>

                <div class="mb-4">
                    <x-forms.input name="password" type="password" label="Passwort" placeholder="••••••••" />
                </div>

                <div class="mb-4">
                    <x-forms.input name="password_confirmation" type="password" label="Passwort bestätigen"
                                   placeholder="••••••••" />
                </div>

                <x-button type="primary" buttonType="submit" class="w-full">
                    {{ __('Passwort zurücksetzen') }}
                </x-button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}"
                   class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Zurück zur Anmeldung') }}</a>
            </div>
        </div>
    </div>
</x-layouts.auth>
