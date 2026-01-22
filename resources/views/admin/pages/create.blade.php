<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Seiten'), 'url' => route('cms.pages.index')],
            ['label' => __('Neue Seite erstellen')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Neue Seite erstellen') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Erstellen Sie eine neue Seite mit Inhalt und SEO-Einstellungen.') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="{{ route('cms.pages.store') }}">
            @csrf

            <!-- Allgemeine Informationen -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b-2 border-gray-200 dark:border-gray-600">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Allgemeine Informationen') }}
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Title -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Titel') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('title') border-red-500 @enderror"
                           placeholder="{{ __('Geben Sie einen aussagekräftigen Titel ein') }}">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="slug" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Slug (URL)') }}
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('slug') border-red-500 @enderror"
                           placeholder="{{ __('wird-automatisch-generiert') }}">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Wird automatisch aus dem Titel generiert, falls leer gelassen') }}
                    </p>
                    @error('slug')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Inhalt') }}
                    </label>
                    <textarea name="content" id="content" rows="10"
                              class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- SEO-Einstellungen -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-y-2 border-gray-200 dark:border-gray-600">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ __('SEO-Einstellungen') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Optimieren Sie Ihre Seite für Suchmaschinen') }}</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Meta Title -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="meta_title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Meta Titel') }}
                    </label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="60"
                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all"
                           placeholder="{{ __('Max. 60 Zeichen für Google-Suchergebnisse') }}">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Dieser Titel erscheint in Suchergebnissen. Falls leer, wird der Seitentitel verwendet.') }}
                    </p>
                </div>

                <!-- Meta Description -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="meta_description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Meta Beschreibung') }}
                    </label>
                    <textarea name="meta_description" id="meta_description" rows="3" maxlength="160"
                              class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all"
                              placeholder="{{ __('Max. 160 Zeichen für Google-Suchergebnisse') }}">{{ old('meta_description') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Diese Beschreibung erscheint unter dem Titel in Suchergebnissen.') }}
                    </p>
                </div>
            </div>

            <!-- Seiten-Einstellungen (Layout & Design) -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-y-2 border-gray-200 dark:border-gray-600">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    {{ __('Seiten-Einstellungen') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Layout, Design und individuelle Anpassungen') }}</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Max Width -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="max_width" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Maximale Seitenbreite') }}
                    </label>
                    <select name="settings[max_width]" id="max_width"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        <option value="container" selected>Standard Container (max-w-7xl, ~1280px)</option>
                        <option value="container-sm">Klein (max-w-3xl, ~768px)</option>
                        <option value="container-md">Mittel (max-w-5xl, ~1024px)</option>
                        <option value="container-lg">Groß (max-w-7xl, ~1280px)</option>
                        <option value="container-xl">Extra Groß (max-w-screen-2xl, ~1536px)</option>
                        <option value="full">Volle Breite (100%)</option>
                    </select>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Definiert die maximale Breite des Seiteninhalts') }}
                    </p>
                </div>

                <!-- Background Color -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="background_color" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Hintergrundfarbe') }}
                    </label>
                    <div class="flex gap-3 items-start">
                        <div class="flex-1">
                            <input type="color" name="settings[background_color]" id="background_color"
                                   value="#ffffff"
                                   class="w-full h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 cursor-pointer">
                        </div>
                        <div class="flex-1">
                            <input type="text" id="background_color_text"
                                   value="#ffffff"
                                   placeholder="#ffffff"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-mono">
                        </div>
                        <button type="button" onclick="setPageBackgroundColor('transparent')"
                                class="px-4 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 rounded-lg transition-all">
                            {{ __('Transparent') }}
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Die Hintergrundfarbe der gesamten Seite') }}
                    </p>
                </div>

                <!-- Custom CSS -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <label for="custom_css" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Individuelles CSS') }}
                    </label>
                    <textarea name="settings[custom_css]" id="custom_css" rows="10"
                              class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-mono text-sm"
                              placeholder="/* Ihr individuelles CSS hier */&#10;.my-custom-class {&#10;    color: #333;&#10;    padding: 1rem;&#10;}"></textarea>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Fügen Sie individuelles CSS hinzu, das nur auf dieser Seite angewendet wird') }}
                    </p>
                </div>
            </div>

            <!-- Veröffentlichung -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-y-2 border-gray-200 dark:border-gray-600">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Veröffentlichung') }}
                </h2>
            </div>

            <div class="p-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_published" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Seite sofort veröffentlichen') }}
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 ml-8">
                        {{ __('Aktivieren Sie diese Option, um die Seite direkt nach dem Speichern öffentlich zugänglich zu machen.') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-100 dark:bg-gray-900 px-6 py-4 border-t-2 border-gray-200 dark:border-gray-600 flex justify-between items-center gap-4">
                <a href="{{ route('cms.pages.index') }}"
                   class="px-6 py-3 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg border-2 border-gray-300 dark:border-gray-600 font-medium transition-all hover:shadow-md">
                    {{ __('Abbrechen') }}
                </a>
                <div class="flex gap-4">
                    <button type="submit" name="action" value="save"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        {{ __('Seite erstellen') }}
                    </button>
                    <button type="submit" name="action" value="save_and_builder"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        {{ __('Erstellen & Page Builder öffnen') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <!-- TinyMCE Local -->
    <script src="{{ asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // TinyMCE
        tinymce.init({
            license_key: 'gpl',
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | removeformat | link image | code | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
            content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
            promotion: false
        });

        // Color Picker Sync
        function setPageBackgroundColor(color) {
            const colorInput = document.getElementById('background_color');
            const textInput = document.getElementById('background_color_text');

            if (color === 'transparent') {
                colorInput.value = '#ffffff';
                textInput.value = 'transparent';
            } else {
                colorInput.value = color;
                textInput.value = color;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const colorInput = document.getElementById('background_color');
            const textInput = document.getElementById('background_color_text');

            if (colorInput && textInput) {
                colorInput.addEventListener('input', function() {
                    textInput.value = this.value;
                });

                textInput.addEventListener('input', function() {
                    if (this.value !== 'transparent' && this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                        colorInput.value = this.value;
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>

