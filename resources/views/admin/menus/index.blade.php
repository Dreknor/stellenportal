<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Menü-Verwaltung')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Menü-Verwaltung') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Menüstruktur verwalten') }}</p>
    </div>

    <!-- Location Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            @foreach($locations as $key => $label)
                <a href="{{ route('cms.menus.index', ['location' => $key]) }}"
                   class="@if($location === $key) border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Current Menu Structure -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktuelle Struktur') }}</h2>

            @if($menuItems->count() > 0)
                <div id="menu-items" class="space-y-2">
                    @foreach($menuItems as $item)
                        @include('admin.menus.partials.menu-item', ['item' => $item, 'level' => 0])
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Keine Menü-Items vorhanden.') }}</p>
            @endif
        </div>

        <!-- Add New Menu Item -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Neues Menü-Item') }}</h2>

            <form method="POST" action="{{ route('cms.menus.store') }}">
                @csrf
                <input type="hidden" name="menu_location" value="{{ $location }}">

                <div class="space-y-4">
                    <!-- Label -->
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Bezeichnung') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" id="label" required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <!-- Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Typ') }}
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="link_type" value="page" checked class="mr-2" onchange="toggleLinkType()">
                                <span class="text-sm">{{ __('Interne Seite') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="link_type" value="url" class="mr-2" onchange="toggleLinkType()">
                                <span class="text-sm">{{ __('Externe URL') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Page Selection -->
                    <div id="page-select">
                        <label for="page_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Seite auswählen') }}
                        </label>
                        <select name="page_id" id="page_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">{{ __('-- Seite wählen --') }}</option>
                            @foreach($pages as $page)
                                <option value="{{ $page->id }}">{{ $page->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- URL Input -->
                    <div id="url-input" style="display: none;">
                        <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('URL') }}
                        </label>
                        <input type="text" name="url" id="url" placeholder="https://example.com"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <!-- Parent Item -->
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Übergeordnetes Item') }}
                        </label>
                        <select name="parent_id" id="parent_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">{{ __('-- Kein (Hauptebene) --') }}</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}">{{ $item->label }}</option>
                                @if($item->children->count() > 0)
                                    @foreach($item->children as $child)
                                        <option value="{{ $child->id }}">-- {{ $child->label }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Target -->
                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Ziel') }}
                        </label>
                        <select name="target" id="target" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            <option value="_self">{{ __('Gleiches Fenster') }}</option>
                            <option value="_blank">{{ __('Neues Fenster') }}</option>
                        </select>
                    </div>

                    <!-- CSS Class -->
                    <div>
                        <label for="css_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('CSS Klasse') }} <span class="text-gray-500 text-xs">({{ __('optional') }})</span>
                        </label>
                        <input type="text" name="css_class" id="css_class"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <!-- Active -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ __('Aktiv') }}
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        {{ __('Hinzufügen') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleLinkType() {
            const linkType = document.querySelector('input[name="link_type"]:checked').value;
            const pageSelect = document.getElementById('page-select');
            const urlInput = document.getElementById('url-input');
            const pageIdField = document.getElementById('page_id');
            const urlField = document.getElementById('url');

            if (linkType === 'page') {
                pageSelect.style.display = 'block';
                urlInput.style.display = 'none';
                pageIdField.required = true;
                urlField.required = false;
                urlField.value = '';
            } else {
                pageSelect.style.display = 'none';
                urlInput.style.display = 'block';
                pageIdField.required = false;
                pageIdField.value = '';
                urlField.required = true;
            }
        }
    </script>
</x-layouts.app>

