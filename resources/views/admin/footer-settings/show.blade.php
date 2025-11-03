<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Footer-Einstellungen'), 'url' => route('admin.footer-settings.index')],
            ['label' => __('Ansicht')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Footer-Einstellung #:id', ['id' => $footerSetting->id]) }}</h1>
        <div class="flex gap-2">
            @can('admin edit organizations')
                @if(!$footerSetting->is_active)
                    <form method="POST" action="{{ route('admin.footer-settings.activate', $footerSetting) }}" class="inline">
                        @csrf
                        <x-button type="success" native-type="submit">
                            <x-fas-check class="w-4 mr-2"/>
                            {{ __('Aktivieren') }}
                        </x-button>
                    </form>
                @endif
                <x-button type="primary" tag="a" :href="route('admin.footer-settings.edit', $footerSetting)">
                    <x-fas-edit class="w-4 mr-2"/>
                    {{ __('Bearbeiten') }}
                </x-button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Logo -->
            @if($footerSetting->logo_path)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Logo') }}</h2>
                    <img src="{{ $footerSetting->logo_url }}" alt="Footer Logo" class="max-h-32 w-auto border border-gray-300 dark:border-gray-600 rounded">
                </div>
            @endif

            <!-- Content -->
            @if($footerSetting->content)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Inhalt') }}</h2>
                    <div class="text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($footerSetting->content)) !!}
                    </div>
                </div>
            @endif

            <!-- Links -->
            @if($footerSetting->links && count($footerSetting->links) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Links') }}</h2>
                    <div class="space-y-3">
                        @foreach($footerSetting->links as $link)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $link['title'] }}</p>
                                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                                       class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $link['url'] }}
                                    </a>
                                </div>
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">
                                    <x-button type="secondary" size="sm">
                                        <x-fas-external-link-alt class="w-3"/>
                                    </x-button>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Status') }}</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aktiv') }}</p>
                        @if($footerSetting->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                <x-fas-check-circle class="w-3 mr-1"/>
                                {{ __('Ja') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                <x-fas-circle class="w-3 mr-1"/>
                                {{ __('Nein') }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Erstellt am') }}</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $footerSetting->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aktualisiert am') }}</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $footerSetting->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Colors -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Farbeinstellungen') }}</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Hintergrundfarbe') }}</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $footerSetting->background_color }};"></div>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $footerSetting->background_color }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Textfarbe') }}</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $footerSetting->text_color }};"></div>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $footerSetting->text_color }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Link-Farbe') }}</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $footerSetting->link_color }};"></div>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $footerSetting->link_color }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Vorschau') }}</h2>
                <div class="border border-gray-300 dark:border-gray-600 rounded overflow-hidden">
                    <footer class="text-center p-4" style="background-color: {{ $footerSetting->background_color }}; color: {{ $footerSetting->text_color }};">
                        @if($footerSetting->logo_path)
                            <div class="mb-3">
                                <img src="{{ $footerSetting->logo_url }}" alt="Logo" class="h-12 w-auto mx-auto">
                            </div>
                        @endif
                        @if($footerSetting->content)
                            <div class="text-xs mb-3" style="color: {{ $footerSetting->text_color }};">
                                {!! nl2br(e($footerSetting->content)) !!}
                            </div>
                        @endif
                        @if($footerSetting->links && count($footerSetting->links) > 0)
                            <div class="flex flex-col items-center space-y-1">
                                @foreach($footerSetting->links as $link)
                                    <a href="{{ $link['url'] }}" class="text-xs hover:underline" style="color: {{ $footerSetting->link_color }};">
                                        {{ $link['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </footer>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

