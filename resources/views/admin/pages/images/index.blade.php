<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Seiten'), 'url' => route('cms.pages.index')],
            ['label' => $page->title, 'url' => route('cms.pages.edit', $page)],
            ['label' => __('Bilder')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Bildverwaltung') }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $page->title }}</p>
        </div>
        <a href="{{ route('cms.pages.edit', $page) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Zurück zur Seite') }}
        </a>
    </div>

    <!-- Upload Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            {{ __('Neues Bild hochladen') }}
        </h2>
        <form method="POST" action="{{ route('cms.pages.images.store', $page) }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Bild') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="image" required accept="image/*"
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Alt-Text') }}
                    </label>
                    <input type="text" name="alt_text" value="{{ old('alt_text') }}"
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                           placeholder="{{ __('Beschreibung für Screenreader und SEO') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Titel') }}
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    {{ __('Hochladen') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Images Gallery -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            {{ __('Hochgeladene Bilder') }} ({{ $images->count() }})
        </h2>

        @if($images->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($images as $image)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden group">
                        <div class="relative">
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <form method="POST" action="{{ route('cms.pages.images.destroy', [$page, $image]) }}"
                                      onsubmit="return confirm('{{ __('Wirklich löschen?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
                                        {{ __('Löschen') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($image->alt_text)
                            <div class="p-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800">
                                {{ $image->alt_text }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-lg font-medium">{{ __('Noch keine Bilder vorhanden') }}</p>
                <p class="text-sm mt-2">{{ __('Laden Sie Ihr erstes Bild hoch.') }}</p>
            </div>
        @endif
    </div>
</x-layouts.app>

