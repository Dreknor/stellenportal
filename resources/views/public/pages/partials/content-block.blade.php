{{-- Text Block --}}
@if($block->type === 'text' && $block->content)
    <div class="prose dark:prose-invert lg:prose-lg max-w-none mb-6">
        {!! nl2br(e($block->content)) !!}
    </div>
@endif

{{-- Heading Block --}}
@if($block->type === 'heading' && $block->content)
    @php
        $level = $block->settings['level'] ?? 'h2';
        $classes = [
            'h1' => 'text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4',
            'h2' => 'text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4',
            'h3' => 'text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-3',
            'h4' => 'text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3',
        ];
        $class = $classes[$level] ?? $classes['h2'];
    @endphp
    @if($level === 'h1')
        <h1 class="{{ $class }}">{{ $block->content }}</h1>
    @elseif($level === 'h2')
        <h2 class="{{ $class }}">{{ $block->content }}</h2>
    @elseif($level === 'h3')
        <h3 class="{{ $class }}">{{ $block->content }}</h3>
    @else
        <h4 class="{{ $class }}">{{ $block->content }}</h4>
    @endif
@endif

{{-- Image Block --}}
@if($block->type === 'image' && isset($block->settings['image_id']) && isset($page))
    @php
        $image = $page->images->firstWhere('id', $block->settings['image_id']);
        $size = $block->settings['size'] ?? 'medium';
        $sizeClasses = [
            'small' => 'max-w-sm',
            'medium' => 'max-w-2xl',
            'large' => 'max-w-4xl',
            'full' => 'w-full',
        ];
        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
    @endphp
    @if($image)
        <figure class="my-8 {{ $sizeClass }} mx-auto">
            <img src="{{ $image->url }}"
                 alt="{{ $image->alt_text }}"
                 class="w-full rounded-lg shadow-lg"
                 @if($image->title) title="{{ $image->title }}" @endif>
            @if($image->alt_text)
                <figcaption class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center">
                    {{ $image->alt_text }}
                </figcaption>
            @endif
        </figure>
    @endif
@endif

{{-- HTML Block --}}
@if($block->type === 'html' && $block->content)
    <div class="my-6">
        {!! $block->content !!}
    </div>
@endif

{{-- Quote Block --}}
@if($block->type === 'quote' && $block->content)
    <blockquote class="my-8 pl-6 border-l-4 border-blue-500 italic text-gray-700 dark:text-gray-300">
        <p class="text-lg mb-2">{{ $block->content }}</p>
        @if(isset($block->settings['author']))
            <footer class="text-sm text-gray-600 dark:text-gray-400 not-italic">
                — {{ $block->settings['author'] }}
            </footer>
        @endif
    </blockquote>
@endif

{{-- Button Block --}}
@if($block->type === 'button' && $block->content && isset($block->settings['url']))
    @php
        $style = $block->settings['style'] ?? 'primary';
        $buttonClasses = [
            'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success' => 'bg-green-600 hover:bg-green-700 text-white',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        ];
        $class = $buttonClasses[$style] ?? $buttonClasses['primary'];
        $target = isset($block->settings['new_tab']) && $block->settings['new_tab'] ? '_blank' : '_self';
    @endphp
    <div class="my-8 text-center">
        <a href="{{ $block->settings['url'] }}"
           target="{{ $target }}"
           @if($target === '_blank') rel="noopener noreferrer" @endif
           class="inline-block px-8 py-3 {{ $class }} rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            {{ $block->content }}
        </a>
    </div>
@endif

{{-- Divider Block --}}
@if($block->type === 'divider')
    @php
        $style = $block->settings['style'] ?? 'solid';
        $spacing = $block->settings['spacing'] ?? 'medium';
        $styleClasses = [
            'solid' => 'border-solid',
            'dashed' => 'border-dashed',
            'dotted' => 'border-dotted',
            'double' => 'border-double',
        ];
        $spacingClasses = [
            'small' => 'my-4',
            'medium' => 'my-8',
            'large' => 'my-12',
        ];
        $styleClass = $styleClasses[$style] ?? $styleClasses['solid'];
        $spacingClass = $spacingClasses[$spacing] ?? $spacingClasses['medium'];
    @endphp
    <hr class="{{ $styleClass }} {{ $spacingClass }} border-t-2 border-gray-300 dark:border-gray-600">
@endif

