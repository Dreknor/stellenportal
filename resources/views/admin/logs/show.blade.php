<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Logs'), 'url' => route('admin.logs.index')],
            ['label' => __('Eintrag #:id', ['id' => $log->id])],
        ];

        $badgeColors = [
            'DEBUG' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'INFO' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'NOTICE' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
            'WARNING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'ERROR' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            'CRITICAL' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'ALERT' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'EMERGENCY' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        ];
        $colorClass = $badgeColors[$log->level_name] ?? $badgeColors['DEBUG'];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Log-Eintrag Details') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Eintrag #:id', ['id' => $log->id]) }}</p>
            </div>
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold {{ $colorClass }}">
                <x-dynamic-component :component="$log->level_icon" class="w-4 h-4 mr-2"/>
                {{ $log->level_name }}
            </span>
        </div>
    </div>

    {{-- Metadata --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Metadaten') }}</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Datum/Zeit') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ $log->created_at->format('d.m.Y H:i:s') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $log->created_at->diffForHumans() }})</span>
                </dd>
            </div>

            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Log-Level') }}</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                        <x-dynamic-component :component="$log->level_icon" class="w-3 h-3 mr-1"/>
                        {{ $log->level_name }}
                    </span>
                </dd>
            </div>

            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Kanal') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-xs">
                        {{ $log->channel ?? '-' }}
                    </span>
                </dd>
            </div>

            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Level-Code') }}</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $log->level }}</dd>
            </div>
        </dl>
    </div>

    {{-- Message --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Nachricht') }}</h2>
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-900 dark:text-gray-100 font-mono whitespace-pre-wrap break-words">{{ $log->message }}</p>
        </div>
    </div>

    {{-- Context --}}
    @if($log->context && !empty($log->context))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                <x-fas-code class="w-4 h-4 inline mr-2"/>
                {{ __('Context / Stack Trace') }}
            </h2>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <pre class="text-xs text-gray-900 dark:text-gray-100 font-mono whitespace-pre-wrap break-words">{{ $log->formatted_context }}</pre>
            </div>

            {{-- Exception Details if present --}}
            @if(isset($log->context['exception']) && is_object($log->context['exception']))
                @php
                    $exception = $log->context['exception'];
                @endphp
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Exception Details') }}</h3>
                    <dl class="grid grid-cols-1 gap-3">
                        @if(method_exists($exception, 'getMessage'))
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Message') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $exception->getMessage() }}</dd>
                            </div>
                        @endif
                        @if(method_exists($exception, 'getFile'))
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('File') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $exception->getFile() }}</dd>
                            </div>
                        @endif
                        @if(method_exists($exception, 'getLine'))
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Line') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $exception->getLine() }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    @endif

    {{-- Extra Data --}}
    @if($log->extra && !empty($log->extra))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Zusätzliche Daten') }}</h2>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <pre class="text-xs text-gray-900 dark:text-gray-100 font-mono">{{ json_encode($log->extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-2">
        <x-button type="secondary" tag="a" :href="route('admin.logs.index')">
            <x-fas-arrow-left class="w-3 mr-2"/>
            {{ __('Zurück zur Übersicht') }}
        </x-button>

        @if($log->level_name === 'ERROR' || $log->level_name === 'CRITICAL')
            <x-button type="secondary" tag="a" :href="route('admin.logs.index', ['level' => strtolower($log->level_name)])">
                <x-fas-filter class="w-3 mr-2"/>
                {{ __('Alle :level Einträge', ['level' => $log->level_name]) }}
            </x-button>
        @endif

        @if($log->channel)
            <x-button type="secondary" tag="a" :href="route('admin.logs.index', ['channel' => $log->channel])">
                <x-fas-filter class="w-3 mr-2"/>
                {{ __('Alle Einträge von :channel', ['channel' => $log->channel]) }}
            </x-button>
        @endif
    </div>
</x-layouts.app>
