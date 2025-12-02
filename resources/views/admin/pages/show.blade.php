<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Seiten'), 'url' => route('cms.pages.index')],
            ['label' => $page->title],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $page->title }}</h1>
        <div class="flex gap-2">
            @can('admin edit pages')
                <a href="{{ route('cms.pages.edit', $page) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    {{ __('Bearbeiten') }}
                </a>
            @endcan
            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                {{ __('Öffentlich ansehen') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Inhalt') }}</h2>
                <div class="prose dark:prose-invert max-w-none">
                    {!! $page->content !!}
                </div>
            </div>

            @if($page->images->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Bilder') }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($page->images as $image)
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                                <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full h-32 object-cover">
                                <div class="p-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $image->alt_text ?? __('Kein Alt-Text') }}
                                </div>
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
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Status') }}</h3>
                @if($page->is_published)
                    <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-sm">
                        {{ __('Veröffentlicht') }}
                    </span>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full text-sm">
                        {{ __('Entwurf') }}
                    </span>
                @endif
            </div>

            <!-- Meta Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Informationen') }}</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Slug') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-100 font-mono">{{ $page->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Erstellt am') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-100">{{ $page->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($page->creator)
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400">{{ __('Erstellt von') }}</dt>
                            <dd class="text-gray-800 dark:text-gray-100">{{ $page->creator->name }}</dd>
                        </div>
                    @endif
                    @if($page->published_at)
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400">{{ __('Veröffentlicht am') }}</dt>
                            <dd class="text-gray-800 dark:text-gray-100">{{ $page->published_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- SEO -->
            @if($page->meta_title || $page->meta_description)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('SEO') }}</h3>
                    @if($page->meta_title)
                        <div class="mb-3">
                            <dt class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Meta Titel') }}</dt>
                            <dd class="text-sm text-gray-800 dark:text-gray-100">{{ $page->meta_title }}</dd>
                        </div>
                    @endif
                    @if($page->meta_description)
                        <div>
                            <dt class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Meta Beschreibung') }}</dt>
                            <dd class="text-sm text-gray-800 dark:text-gray-100">{{ $page->meta_description }}</dd>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

