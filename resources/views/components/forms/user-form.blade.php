@props([
    'action',
    'method' => 'POST',
    'user' => null,
    'submitText' => 'Benutzer anlegen',
    'showCancel' => false,
    'cancelUrl' => null
])

<form action="{{ $action }}" method="POST" class="max-w-3xl">
    @csrf
    @if(strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <x-forms.input
            label="Vorname"
            name="first_name"
            type="text"
            value="{{ old('first_name', $user->first_name ?? '') }}"
            required
        />

        <x-forms.input
            label="Nachname"
            name="last_name"
            type="text"
            value="{{ old('last_name', $user->last_name ?? '') }}"
            required
        />
    </div>

    <div class="mb-4">
        <x-forms.input
            label="E-Mail"
            name="email"
            type="email"
            value="{{ old('email', $user->email ?? '') }}"
            required
        />
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Der Benutzer erhält eine E-Mail mit einem temporären Passwort.') }}
        </p>
    </div>



    <div class="flex gap-4">
        <x-button type="primary">{{ __($submitText) }}</x-button>

        @if($showCancel && $cancelUrl)
            <x-button type="secondary" tag="a" :href="$cancelUrl">
                {{ __('Abbrechen') }}
            </x-button>
        @endif
    </div>
</form>

