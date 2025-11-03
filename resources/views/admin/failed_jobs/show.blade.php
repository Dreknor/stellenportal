<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Fehlgeschlagene Jobs'), 'url' => route('admin.failed-jobs.index')],
            ['label' => __('Details')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Fehlgeschlagener Job') }} #{{ $job->id }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Details zum fehlgeschlagenen Queue-Job') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <dl class="grid grid-cols-1 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Queue') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->queue ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Verbindung') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->connection ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Fehlerzeit') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($job->failed_at)->format('d.m.Y H:i:s') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Exception') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"><pre class="whitespace-pre-wrap">{{ $job->exception }}</pre></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Payload') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"><pre class="whitespace-pre-wrap">{{ json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></dd>
            </div>
        </dl>
    </div>

    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->id) }}" class="inline-block">@csrf
            <x-button type="primary">{{ __('Neu starten') }}</x-button>
        </form>
        <form method="POST" action="{{ route('admin.failed-jobs.destroy', $job->id) }}" class="inline-block" onsubmit="return confirm('{{ __('Möchten Sie diesen fehlgeschlagenen Job wirklich löschen?') }}');">
            @method('DELETE')
            @csrf
            <x-button type="secondary">{{ __('Löschen') }}</x-button>
        </form>
        <x-button type="secondary" tag="a" :href="route('admin.failed-jobs.index')">{{ __('Zurück') }}</x-button>
    </div>
</x-layouts.app>

