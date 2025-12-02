{{-- Text Block Preview --}}
@if($block->type === 'text')
    <div class="prose dark:prose-invert max-w-none">
        {{ Str::limit(strip_tags($block->content), 200) }}
    </div>
@endif

{{-- Heading Block Preview --}}
@if($block->type === 'heading')
    <div class="font-bold text-xl text-gray-800 dark:text-gray-100">
        {{ $block->content }}
        @if(isset($block->settings['level']))
            <span class="ml-2 text-xs text-gray-500">({{ strtoupper($block->settings['level']) }})</span>
        @endif
    </div>
@endif

{{-- Image Block Preview --}}
@if($block->type === 'image')
    @if(isset($block->settings['image_id']) && isset($images))
        @php
            $image = $images->firstWhere('id', $block->settings['image_id']);
        @endphp
        @if($image)
            <div class="flex items-center gap-3">
                <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300 dark:border-gray-600">
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $image->alt_text ?: 'Bild' }}</p>
                    <p class="text-xs text-gray-500">Größe: {{ ucfirst($block->settings['size'] ?? 'medium') }}</p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 italic">{{ __('Bild nicht gefunden (ID: ' . $block->settings['image_id'] . ')') }}</p>
        @endif
    @else
        <p class="text-sm text-gray-500 italic">{{ __('Kein Bild ausgewählt') }}</p>
    @endif
@endif

{{-- HTML Block Preview --}}
@if($block->type === 'html')
    <div class="bg-gray-100 dark:bg-gray-800 rounded p-3 font-mono text-xs">
        {{ Str::limit($block->content, 150) }}
    </div>
@endif

{{-- Quote Block Preview --}}
@if($block->type === 'quote')
    <div class="border-l-4 border-blue-500 pl-4 italic text-gray-700 dark:text-gray-300">
        <p>{{ Str::limit($block->content, 150) }}</p>
        @if(isset($block->settings['author']))
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">— {{ $block->settings['author'] }}</p>
        @endif
    </div>
@endif

{{-- Button Block Preview --}}
@if($block->type === 'button')
    <div class="flex items-center gap-3">
        @php
            $buttonClasses = [
                'primary' => 'bg-blue-600 text-white',
                'secondary' => 'bg-gray-500 text-white',
                'success' => 'bg-green-600 text-white',
                'danger' => 'bg-red-600 text-white',
            ];
            $style = $block->settings['style'] ?? 'primary';
        @endphp
        <span class="inline-block px-4 py-2 rounded-lg {{ $buttonClasses[$style] ?? $buttonClasses['primary'] }}">
            {{ $block->content ?: 'Button' }}
        </span>
        <div class="text-sm text-gray-500">
            @if(isset($block->settings['url']))
                <p>→ {{ Str::limit($block->settings['url'], 50) }}</p>
            @endif
            @if(isset($block->settings['new_tab']) && $block->settings['new_tab'])
                <p class="text-xs">(Neues Fenster)</p>
            @endif
        </div>
    </div>
@endif

{{-- Divider Block Preview --}}
@if($block->type === 'divider')
    @php
        $styles = [
            'solid' => 'border-solid',
            'dashed' => 'border-dashed',
            'dotted' => 'border-dotted',
            'double' => 'border-double',
        ];
        $style = $styles[$block->settings['style'] ?? 'solid'] ?? 'border-solid';
    @endphp
    <div class="flex items-center gap-3">
        <hr class="flex-1 border-t-2 {{ $style }} border-gray-400 dark:border-gray-600">
        <span class="text-xs text-gray-500">
            {{ ucfirst($block->settings['style'] ?? 'solid') }} | {{ ucfirst($block->settings['spacing'] ?? 'medium') }}
        </span>
    </div>
@endif

