<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Audit-Logs'), 'url' => route('admin.audits.index')],
            ['label' => __('Details')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Audit-Log Details') }}</h1>
        <x-button type="secondary" tag="a" :href="route('admin.audits.index')">
            <x-fas-arrow-left class="w-3 mr-2"/>
            {{ __('Zurück zur Übersicht') }}
        </x-button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Grundinformationen') }}</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Event') }}</dt>
                        <dd class="mt-1">
                            @if($audit->event === 'created')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    <x-fas-plus class="w-3 mr-1"/>
                                    {{ __('Erstellt') }}
                                </span>
                            @elseif($audit->event === 'updated')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    <x-fas-edit class="w-3 mr-1"/>
                                    {{ __('Aktualisiert') }}
                                </span>
                            @elseif($audit->event === 'deleted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    <x-fas-trash class="w-3 mr-1"/>
                                    {{ __('Gelöscht') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                    {{ ucfirst($audit->event) }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Zeitpunkt') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $audit->created_at->format('d.m.Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Modell') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ class_basename($audit->auditable_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Modell ID') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $audit->auditable_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('IP-Adresse') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $audit->ip_address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User Agent') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 truncate" title="{{ $audit->user_agent }}">
                            {{ $audit->user_agent ? Str::limit($audit->user_agent, 50) : '-' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Old Values -->
            @if($audit->old_values && count($audit->old_values) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                        <x-fas-history class="w-5 h-5 mr-2 text-red-500"/>
                        {{ __('Alte Werte') }}
                    </h2>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-800 dark:text-gray-200">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

            <!-- New Values -->
            @if($audit->new_values && count($audit->new_values) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                        <x-fas-check-circle class="w-5 h-5 mr-2 text-green-500"/>
                        {{ __('Neue Werte') }}
                    </h2>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-800 dark:text-gray-200">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

            <!-- Modified Values (Diff) -->
            @if($audit->event === 'updated' && $audit->old_values && $audit->new_values)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                        <x-fas-exchange-alt class="w-5 h-5 mr-2 text-blue-500"/>
                        {{ __('Geänderte Felder') }}
                    </h2>
                    <div class="space-y-3">
                        @foreach($audit->new_values as $key => $newValue)
                            @if(isset($audit->old_values[$key]) && $audit->old_values[$key] != $newValue)
                                <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                    <div class="font-medium text-sm text-gray-800 dark:text-gray-200 mb-1">{{ $key }}</div>
                                    <div class="grid grid-cols-2 gap-4 text-xs">
                                        <div>
                                            <span class="text-red-600 dark:text-red-400 font-semibold">{{ __('Vorher:') }}</span>
                                            <div class="mt-1 text-gray-700 dark:text-gray-300 break-all">
                                                {{ is_array($audit->old_values[$key]) ? json_encode($audit->old_values[$key]) : $audit->old_values[$key] }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-green-600 dark:text-green-400 font-semibold">{{ __('Nachher:') }}</span>
                                            <div class="mt-1 text-gray-700 dark:text-gray-300 break-all">
                                                {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Benutzer') }}</h2>
                @if($audit->user)
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($audit->user->first_name, 0, 1) . substr($audit->user->last_name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $audit->user->first_name }} {{ $audit->user->last_name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $audit->user->email }}</div>
                        </div>
                    </div>
                    <x-button type="secondary" size="sm" tag="a" :href="route('admin.users.show', $audit->user)" class="w-full">
                        <x-fas-user class="w-3 mr-2"/>
                        {{ __('Profil anzeigen') }}
                    </x-button>
                @else
                    <div class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        <x-fas-robot class="w-8 h-8 mx-auto mb-2 opacity-50"/>
                        <p>{{ __('System') }}</p>
                    </div>
                @endif
            </div>

            <!-- Technical Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Technische Details') }}</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Audit ID') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $audit->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Vollständiger Modellname') }}</dt>
                        <dd class="mt-1 text-xs text-gray-900 dark:text-gray-100 font-mono break-all">{{ $audit->auditable_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('URL') }}</dt>
                        <dd class="mt-1 text-xs text-gray-900 dark:text-gray-100 break-all">{{ $audit->url ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Tags') }}</dt>
                        <dd class="mt-1">
                            @if($audit->tags)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode(',', $audit->tags) as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ trim($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-layouts.app>

