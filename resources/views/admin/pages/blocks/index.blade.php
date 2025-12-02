<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Seiten'), 'url' => route('cms.pages.index')],
            ['label' => $page->title, 'url' => route('cms.pages.edit', $page)],
            ['label' => __('Page Builder')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Page Builder') }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $page->title }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('cms.pages.preview', $page) }}" target="_blank" class="px-4 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ __('Vorschau') }}
            </a>
            <a href="{{ route('cms.pages.edit', $page) }}" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('Editor') }}
            </a>
        </div>
    </div>

    <!-- Add Block Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ __('Neuen Block hinzufügen') }}
        </h2>

        <form method="POST" action="{{ route('cms.pages.blocks.store', $page) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Block-Typ') }}
                </label>
                <select name="type" id="type" required class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    @foreach($blockTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Block hinzufügen') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Content Blocks -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b-2 border-gray-200 dark:border-gray-600">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                {{ __('Content Blocks') }} ({{ $page->contentBlocks->count() }})
            </h2>
        </div>

        <div class="p-6">
            @if($page->contentBlocks->isEmpty())
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-medium">{{ __('Noch keine Blöcke vorhanden') }}</p>
                    <p class="text-sm mt-2">{{ __('Fügen Sie Ihren ersten Block hinzu, um zu beginnen.') }}</p>
                </div>
            @else
                <div class="space-y-4" id="blocks-container">
                    @foreach($page->contentBlocks as $block)
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-colors" data-block-id="{{ $block->id }}">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-400 cursor-move drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">
                                        {{ $blockTypes[$block->type] ?? $block->type }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Reihenfolge') }}: {{ $block->order }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="toggleEdit({{ $block->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('cms.pages.blocks.destroy', [$page, $block]) }}" class="inline" onsubmit="return confirm('{{ __('Wirklich löschen?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Block Content Preview -->
                            <div class="mb-4">
                                @include('admin.pages.blocks.partials.block-preview', ['block' => $block, 'images' => $images])
                            </div>

                            <!-- Edit Form (Hidden by default) -->
                            <div id="edit-{{ $block->id }}" class="hidden mt-4 pt-4 border-t dark:border-gray-600">
                                <form method="POST" action="{{ route('cms.pages.blocks.update', [$page, $block]) }}">
                                    @csrf
                                    @method('PUT')

                                    @include('admin.pages.blocks.partials.block-edit-form', ['block' => $block, 'page' => $page, 'images' => $images])

                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="toggleEdit({{ $block->id }})" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 rounded-lg">
                                            {{ __('Abbrechen') }}
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                            {{ __('Speichern') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function toggleEdit(blockId) {
            const editForm = document.getElementById('edit-' + blockId);
            editForm.classList.toggle('hidden');
        }

        // Initialize Sortable for drag & drop
        @if($page->contentBlocks->isNotEmpty())
        const container = document.getElementById('blocks-container');
        Sortable.create(container, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                // Collect new order
                const blocks = [];
                container.querySelectorAll('[data-block-id]').forEach((el, index) => {
                    blocks.push({
                        id: el.dataset.blockId,
                        order: index
                    });
                });

                // Send to server
                fetch('{{ route('cms.pages.blocks.reorder', $page) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ blocks })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show updated order
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error updating order:', error);
                    alert('Fehler beim Aktualisieren der Reihenfolge');
                });
            }
        });
        @endif
    </script>
    @endpush
</x-layouts.app>

