{{-- Block Item with nested support --}}
<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-colors block-item {{ $block->type === 'row' ? 'row-container' : '' }}"
     data-block-id="{{ $block->id }}"
     data-block-type="{{ $block->type }}"
     style="margin-left: {{ $level * 2 }}rem;">

    <div class="p-6">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400 cursor-move drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                </svg>
                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">
                    {{ $blockTypes[$block->type] ?? $block->type }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Reihenfolge') }}: {{ $block->order }}</span>
                @if($level > 0)
                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded text-xs">
                        {{ __('Verschachtelt') }}
                    </span>
                @endif
            </div>
            <div class="flex gap-2">
                @if($block->type === 'row' || $block->type === 'columns')
                    <button onclick="showAddChildModal({{ $block->id }})"
                            class="text-green-600 hover:text-green-800 dark:text-green-400"
                            title="{{ __('Block hinzufügen') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                @endif
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

    <!-- Nested Children (for row and columns containers) -->
    @if($block->type === 'row' || $block->type === 'columns')
        <div class="children-container bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 p-4 border-t-2 border-dashed border-purple-300 dark:border-purple-700" data-parent-id="{{ $block->id }}">
            <div class="flex justify-between items-center mb-3">
                <div class="text-xs text-purple-700 dark:text-purple-300 font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    {{ __('Verschachtelte Blöcke') }} ({{ $block->children->count() }})
                </div>
                <button onclick="showAddChildModal({{ $block->id }})" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors">
                    {{ __('+ Block hinzufügen') }}
                </button>
            </div>

            @if($block->children->count() > 0)
                <div class="space-y-3 nested-blocks-container" data-parent-id="{{ $block->id }}">
                    @foreach($block->children as $childBlock)
                        @include('admin.pages.blocks.partials.block-item', ['block' => $childBlock, 'blockTypes' => $blockTypes, 'images' => $images, 'page' => $page, 'level' => $level + 1])
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400 nested-blocks-container" data-parent-id="{{ $block->id }}">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm">{{ __('Noch keine Blöcke. Ziehen Sie Blöcke hierher oder klicken Sie auf "+ Block hinzufügen"') }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
