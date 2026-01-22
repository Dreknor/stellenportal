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

{{-- Row Block Preview --}}
@if($block->type === 'row')
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 p-4 rounded-lg border-2 border-purple-200 dark:border-purple-700">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path>
            </svg>
            <span class="font-semibold text-purple-800 dark:text-purple-200">Container/Reihe</span>
        </div>
        <div class="text-sm text-gray-700 dark:text-gray-300">
            <p><strong>Breite:</strong> {{ ucfirst($block->settings['width'] ?? 'container') }}</p>
            @if($block->background_color)
                <p class="flex items-center gap-2">
                    <strong>Hintergrund:</strong>
                    <span class="inline-block w-6 h-6 rounded border-2 border-gray-300" style="background-color: {{ $block->background_color }}"></span>
                    <span class="font-mono text-xs">{{ $block->background_color }}</span>
                </p>
            @else
                <p><strong>Hintergrund:</strong> Transparent</p>
            @endif
            <p><strong>Padding:</strong> {{ ucfirst($block->settings['padding'] ?? 'medium') }}</p>
            @if($block->children && $block->children->count() > 0)
                <p class="mt-2 text-purple-700 dark:text-purple-300"><strong>Verschachtelt:</strong> {{ $block->children->count() }} Block(s)</p>
            @endif
        </div>
    </div>
@endif

{{-- Columns Block Preview --}}
@if($block->type === 'columns')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 p-4 rounded-lg border-2 border-blue-200 dark:border-blue-700">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a1 1 0 00-1 1v14a1 1 0 001 1h4m0-16v16m0-16h6m-6 16h6m0-16h4a1 1 0 011 1v14a1 1 0 01-1 1h-4m0-16v16"></path>
            </svg>
            <span class="font-semibold text-blue-800 dark:text-blue-200">Spalten-Layout</span>
        </div>
        <div class="text-sm text-gray-700 dark:text-gray-300 mb-3">
            <p><strong>Anzahl:</strong> {{ $block->settings['columns'] ?? '2' }} Spalten</p>
            <p><strong>Abstand:</strong> {{ ucfirst($block->settings['gap'] ?? 'medium') }}</p>
            @if($block->settings['equal_height'] ?? false)
                <p><strong>Gleiche Höhe:</strong> Ja</p>
            @endif
            @if($block->children && $block->children->count() > 0)
                <p class="mt-2 text-purple-700 dark:text-purple-300"><strong>Verschachtelt:</strong> {{ $block->children->count() }} Block(s)</p>
            @endif
        </div>
        @if($block->content)
            <div class="text-xs text-gray-600 dark:text-gray-400 font-mono bg-white dark:bg-gray-800 p-2 rounded">
                {{ Str::limit(strip_tags($block->content), 100) }}
            </div>
        @endif
    </div>
@endif

{{-- Card Block Preview --}}
@if($block->type === 'card')
    <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-lg p-4 shadow-md max-w-sm">
        @if(isset($block->settings['icon']))
            <div class="mb-3">
                <i class="fas {{ $block->settings['icon'] }} text-3xl text-blue-600"></i>
            </div>
        @endif
        @if(isset($block->settings['title']))
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $block->settings['title'] }}</h3>
        @endif
        @if($block->content)
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($block->content, 100) }}</p>
        @endif
        @if(isset($block->settings['button_text']))
            <span class="inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded">{{ $block->settings['button_text'] }}</span>
        @endif
        <div class="mt-2 text-xs text-gray-500">
            Stil: {{ ucfirst($block->settings['style'] ?? 'default') }}
        </div>
    </div>
@endif

{{-- Card with Image Block Preview --}}
@if($block->type === 'card_image')
    @if(isset($block->settings['image_id']) && isset($images))
        @php
            $image = $images->firstWhere('id', $block->settings['image_id']);
        @endphp
        @if($image)
            <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden shadow-md max-w-sm">
                <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full h-40 object-cover">
                <div class="p-4">
                    @if(isset($block->settings['title']))
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $block->settings['title'] }}</h3>
                    @endif
                    @if($block->content)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($block->content, 80) }}</p>
                    @endif
                    @if(isset($block->settings['button_text']))
                        <span class="inline-block px-3 py-1.5 bg-blue-600 text-white text-xs rounded">{{ $block->settings['button_text'] }}</span>
                    @endif
                </div>
                <div class="px-4 pb-2 text-xs text-gray-500">
                    Position: {{ ucfirst($block->settings['image_position'] ?? 'top') }}
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 italic">{{ __('Bild nicht gefunden') }}</p>
        @endif
    @else
        <p class="text-sm text-gray-500 italic">{{ __('Kein Bild ausgewählt') }}</p>
    @endif
@endif

{{-- Block-Einstellungen Anzeige --}}
@php
    $hasBlockSettings = isset($block->settings['block_max_width'])
        || isset($block->settings['block_background_color'])
        || isset($block->settings['block_margin'])
        || isset($block->settings['block_padding'])
        || isset($block->settings['block_rounded'])
        || isset($block->settings['block_css_class'])
        || isset($block->settings['block_custom_css']);
@endphp

@if($hasBlockSettings)
    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-400">{{ __('Block-Einstellungen aktiv') }}</span>
        </div>
        <div class="flex flex-wrap gap-1.5 text-xs">
            @if(isset($block->settings['block_max_width']))
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                    Breite: {{ strtoupper($block->settings['block_max_width']) }}
                </span>
            @endif
            @if(isset($block->settings['block_background_color']) && $block->settings['block_background_color'] !== 'transparent')
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                    BG: {{ $block->settings['block_background_color'] }}
                </span>
            @endif
            @if(isset($block->settings['block_margin']))
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                    Margin: {{ $block->settings['block_margin'] }}
                </span>
            @endif
            @if(isset($block->settings['block_padding']))
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                    Padding: {{ $block->settings['block_padding'] }}
                </span>
            @endif
            @if(isset($block->settings['block_rounded']))
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                    Rundung: {{ $block->settings['block_rounded'] }}
                </span>
            @endif
            @if(isset($block->settings['block_css_class']))
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded font-mono">
                    .{{ $block->settings['block_css_class'] }}
                </span>
            @endif
            @if(isset($block->settings['block_custom_css']))
                <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded">
                    Custom CSS ✓
                </span>
            @endif
        </div>
    </div>
@endif
