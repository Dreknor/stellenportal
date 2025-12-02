<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 mb-2" style="margin-left: {{ $level * 20 }}px;">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $item->label }}</span>
                @if(!$item->is_active)
                    <span class="text-xs px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded">{{ __('Inaktiv') }}</span>
                @endif
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                @if($item->page_id)
                    <span class="text-blue-600 dark:text-blue-400">{{ __('Seite') }}: {{ $item->page->title ?? __('Gelöscht') }}</span>
                @else
                    <span class="text-purple-600 dark:text-purple-400">{{ __('URL') }}: {{ $item->url }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('cms.menus.destroy', $item) }}"
                  onsubmit="return confirm('{{ __('Sind Sie sicher?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 text-sm">
                    {{ __('Löschen') }}
                </button>
            </form>
        </div>
    </div>
</div>

@if($item->children->count() > 0)
    @foreach($item->children as $child)
        @include('admin.menus.partials.menu-item', ['item' => $child, 'level' => $level + 1])
    @endforeach
@endif

