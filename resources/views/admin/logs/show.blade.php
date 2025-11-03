<x-layouts.app>
    @php
        // Sicherstellen, dass Variablen vorhanden sind (statische Analyse / Fallback)
        $file = $file ?? '';
        $lines = $lines ?? [];

        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Logs'), 'url' => route('admin.logs.index')],
            ['label' => $file],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $file }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Letzte Zeilen der Logdatei') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <pre class="whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">@foreach($lines as $line){{ $line }}
@endforeach</pre>
    </div>

    <div class="flex gap-2">
        <x-button type="secondary" tag="a" :href="route('admin.logs.download', $file)">
            <x-fas-download class="w-3 mr-2"/>
            {{ __('Herunterladen') }}
        </x-button>
        <x-button type="secondary" tag="a" :href="route('admin.logs.index')">{{ __('Zurück') }}</x-button>
    </div>
</x-layouts.app>
