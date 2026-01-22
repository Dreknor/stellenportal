@php
    // Block-Einstellungen extrahieren
    $blockSettings = $block->settings ?? [];

    // Breite
    $blockMaxWidth = $blockSettings['block_max_width'] ?? '';
    $widthClasses = [
        'sm' => 'max-w-3xl',
        'md' => 'max-w-5xl',
        'lg' => 'max-w-7xl',
        'xl' => 'max-w-screen-2xl',
    ];
    $widthClass = $widthClasses[$blockMaxWidth] ?? '';

    // Margin
    $blockMargin = $blockSettings['block_margin'] ?? '';
    $marginClasses = [
        'none' => 'my-0',
        'sm' => 'my-2',
        'md' => 'my-4',
        'lg' => 'my-8',
        'xl' => 'my-12',
    ];
    $marginClass = $marginClasses[$blockMargin] ?? 'my-6';

    // Padding (nur für Wrapper, nicht für Card-Blöcke)
    $blockPadding = $blockSettings['block_padding'] ?? '';
    $paddingClasses = [
        'none' => 'p-0',
        'sm' => 'p-2',
        'md' => 'p-4',
        'lg' => 'p-8',
        'xl' => 'p-12',
    ];
    $paddingClass = $paddingClasses[$blockPadding] ?? '';

    // Border Radius (nur für Wrapper, nicht für Card-Blöcke)
    $blockRounded = $blockSettings['block_rounded'] ?? '';
    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];
    $roundedClass = $roundedClasses[$blockRounded] ?? '';

    // Hintergrundfarbe
    $blockBgColor = $blockSettings['block_background_color'] ?? 'transparent';

    // Für Card-Blöcke: Hintergrundfarbe wird auf die Card angewendet, nicht auf den Wrapper
    $isCardBlock = in_array($block->type, ['card', 'card_image']);

    $bgStyle = '';
    $wrapperBgStyle = '';

    if ($blockBgColor && $blockBgColor !== 'transparent') {
        if ($isCardBlock) {
            // Bei Card-Blöcken: Farbe für die Card speichern, nicht für Wrapper
            $bgStyle = 'background-color: ' . e($blockBgColor) . ';';
        } else {
            // Bei anderen Blöcken: Normal auf Wrapper anwenden
            $wrapperBgStyle = 'background-color: ' . e($blockBgColor) . ';';
        }
    }

    // Custom CSS Class
    $customClass = $blockSettings['block_css_class'] ?? '';

    // Custom CSS
    $customCss = $blockSettings['block_custom_css'] ?? '';
    $blockIdClass = 'content-block-' . $block->id;

    // Alle Klassen zusammenführen
    // Bei Card-Blöcken: Kein Padding/Rounded auf Wrapper
    if ($isCardBlock) {
        $wrapperClasses = trim("$widthClass $marginClass $customClass $blockIdClass");
    } else {
        $wrapperClasses = trim("$widthClass $marginClass $paddingClass $roundedClass $customClass $blockIdClass");
    }
@endphp

@if(!empty($customCss))
    <style>
        .{{ $blockIdClass }} {
            {!! $customCss !!}
        }
    </style>
@endif

<div class="{{ $wrapperClasses }}" @if($wrapperBgStyle) style="{{ $wrapperBgStyle }}" @endif>

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

{{-- Row/Container Block --}}
@if($block->type === 'row')
    @php
        $width = $block->settings['width'] ?? 'container';
        $padding = $block->settings['padding'] ?? 'medium';
        $bgColor = $block->background_color ?? 'transparent';

        $widthClasses = [
            'full' => 'w-full',
            'container' => 'container mx-auto',
            'narrow' => 'max-w-4xl mx-auto',
        ];

        $paddingClasses = [
            'none' => '',
            'small' => 'py-4 px-4',
            'medium' => 'py-8 px-6',
            'large' => 'py-12 px-8',
        ];

        $widthClass = $widthClasses[$width] ?? $widthClasses['container'];
        $paddingClass = $paddingClasses[$padding] ?? $paddingClasses['medium'];

        // Handle background color
        $bgStyle = '';
        if ($bgColor !== 'transparent') {
            $bgStyle = 'background-color: ' . $bgColor . ';';
        }
    @endphp
    <div class="{{ $paddingClass }} my-6" style="{{ $bgStyle }}">
        <div class="{{ $widthClass }}">
            @if(isset($block->children) && $block->children->count() > 0)
                {{-- Render nested blocks --}}
                @foreach($block->children->where('is_visible', true) as $childBlock)
                    @include('public.pages.partials.content-block', ['block' => $childBlock, 'page' => $page])
                @endforeach
            @elseif($block->content)
                {{-- Fallback: render content if no children --}}
                {!! $block->content !!}
            @endif
        </div>
    </div>
@endif

{{-- Columns Block --}}
@if($block->type === 'columns')
    @php
        $columns = $block->settings['columns'] ?? '2';
        $gap = $block->settings['gap'] ?? 'medium';
        $equalHeight = $block->settings['equal_height'] ?? false;

        $columnClasses = [
            '1' => 'md:grid-cols-1',
            '2' => 'md:grid-cols-2',
            '3' => 'md:grid-cols-3',
            '4' => 'md:grid-cols-4',
        ];

        $gapClasses = [
            'small' => 'gap-4',
            'medium' => 'gap-6',
            'large' => 'gap-8',
        ];

        $columnClass = $columnClasses[$columns] ?? $columnClasses['2'];
        $gapClass = $gapClasses[$gap] ?? $gapClasses['medium'];
        $heightClass = $equalHeight ? 'items-stretch' : '';
    @endphp
    <div class="grid grid-cols-1 {{ $columnClass }} {{ $gapClass }} {{ $heightClass }} my-8">
        @if(isset($block->children) && $block->children->count() > 0)
            {{-- Render nested blocks as columns --}}
            @foreach($block->children->where('is_visible', true) as $childBlock)
                <div class="@if($equalHeight) flex flex-col @endif">
                    @include('public.pages.partials.content-block', ['block' => $childBlock, 'page' => $page])
                </div>
            @endforeach
        @elseif($block->content)
            {{-- Fallback: Parse content into column divs for legacy support --}}
            @php
                preg_match_all('/<div[^>]*>(.*?)<\/div>/s', $block->content, $matches);
                $columnContents = $matches[0] ?? [];
            @endphp
            @if(count($columnContents) > 0)
                @foreach($columnContents as $columnContent)
                    <div class="@if($equalHeight) flex flex-col @endif">
                        {!! $columnContent !!}
                    </div>
                @endforeach
            @else
                {{-- Fallback if no proper divs found --}}
                <div class="col-span-full">
                    {!! $block->content !!}
                </div>
            @endif
        @endif
    </div>
@endif

{{-- Card Block --}}
@if($block->type === 'card')
    @php
        $title = $block->settings['title'] ?? '';
        $icon = $block->settings['icon'] ?? '';
        $buttonText = $block->settings['button_text'] ?? '';
        $buttonUrl = $block->settings['button_url'] ?? '';
        $style = $block->settings['style'] ?? 'default';

        $styleClasses = [
            'default' => 'bg-white dark:bg-gray-800 rounded-lg p-6',
            'bordered' => 'bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6',
            'shadow' => 'bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg',
            'elevated' => 'bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl transform hover:scale-105 transition-transform',
        ];

        $cardClass = $styleClasses[$style] ?? $styleClasses['default'];

        // Wende Block-Padding und Rounded auf die Card an (wenn gesetzt)
        if (!empty($paddingClass)) {
            $cardClass .= ' ' . $paddingClass;
        }
        if (!empty($roundedClass)) {
            // Ersetze das Standard rounded-lg mit der benutzerdefinierten Rundung
            $cardClass = preg_replace('/rounded-\w+/', $roundedClass, $cardClass);
        }
    @endphp
    <div class="{{ $cardClass }} my-6" @if($bgStyle) style="{{ $bgStyle }}" @endif>
        @if($icon)
            <div class="mb-4">
                <i class="fas {{ $icon }} text-4xl text-blue-600 dark:text-blue-400"></i>
            </div>
        @endif

        @if($title)
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">{{ $title }}</h3>
        @endif

        @if($block->content)
            <div class="prose dark:prose-invert max-w-none mb-4 text-gray-600 dark:text-gray-300">
                {!! nl2br(e($block->content)) !!}
            </div>
        @endif

        @if($buttonText && $buttonUrl)
            <a href="{{ $buttonUrl }}"
               class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                {{ $buttonText }}
            </a>
        @endif
    </div>
@endif

{{-- Card with Image Block --}}
@if($block->type === 'card_image' && isset($block->settings['image_id']) && isset($page))
    @php
        $image = $page->images->firstWhere('id', $block->settings['image_id']);
        $title = $block->settings['title'] ?? '';
        $buttonText = $block->settings['button_text'] ?? '';
        $buttonUrl = $block->settings['button_url'] ?? '';
        $imagePosition = $block->settings['image_position'] ?? 'top';

        // Standard Card-Klassen
        $cardImageClass = 'bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden my-6';

        // Wende Block-Rounded auf die Card an (wenn gesetzt)
        if (!empty($roundedClass)) {
            $cardImageClass = preg_replace('/rounded-\w+/', $roundedClass, $cardImageClass);
        }

        // Standard Padding für Card-Body
        $cardBodyClass = 'p-6';
        // Wende Block-Padding auf Card-Body an (wenn gesetzt)
        if (!empty($paddingClass)) {
            $cardBodyClass = $paddingClass;
        }
    @endphp

    @if($image)
        @if($imagePosition === 'top')
            {{-- Image on top --}}
            <div class="{{ $cardImageClass }} max-w-2xl mx-auto" @if($bgStyle) style="{{ $bgStyle }}" @endif>
                <img src="{{ $image->url }}"
                     alt="{{ $image->alt_text }}"
                     class="w-full h-64 object-cover">
                <div class="{{ $cardBodyClass }}">
                    @if($title)
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">{{ $title }}</h3>
                    @endif
                    @if($block->content)
                        <div class="prose dark:prose-invert max-w-none mb-4 text-gray-600 dark:text-gray-300">
                            {!! nl2br(e($block->content)) !!}
                        </div>
                    @endif
                    @if($buttonText && $buttonUrl)
                        <a href="{{ $buttonUrl }}"
                           class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                            {{ $buttonText }}
                        </a>
                    @endif
                </div>
            </div>
        @elseif($imagePosition === 'left')
            {{-- Image on left --}}
            <div class="{{ $cardImageClass }}" @if($bgStyle) style="{{ $bgStyle }}" @endif>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    <img src="{{ $image->url }}"
                         alt="{{ $image->alt_text }}"
                         class="w-full h-full object-cover min-h-[300px]">
                    <div class="{{ $cardBodyClass }} flex flex-col justify-center">
                        @if($title)
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">{{ $title }}</h3>
                        @endif
                        @if($block->content)
                            <div class="prose dark:prose-invert max-w-none mb-4 text-gray-600 dark:text-gray-300">
                                {!! nl2br(e($block->content)) !!}
                            </div>
                        @endif
                        @if($buttonText && $buttonUrl)
                            <a href="{{ $buttonUrl }}"
                               class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all self-start">
                                {{ $buttonText }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            {{-- Image on right --}}
            <div class="{{ $cardImageClass }}" @if($bgStyle) style="{{ $bgStyle }}" @endif>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    <div class="{{ $cardBodyClass }} flex flex-col justify-center">
                        @if($title)
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">{{ $title }}</h3>
                        @endif
                        @if($block->content)
                            <div class="prose dark:prose-invert max-w-none mb-4 text-gray-600 dark:text-gray-300">
                                {!! nl2br(e($block->content)) !!}
                            </div>
                        @endif
                        @if($buttonText && $buttonUrl)
                            <a href="{{ $buttonUrl }}"
                               class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all self-start">
                                {{ $buttonText }}
                            </a>
                        @endif
                    </div>
                    <img src="{{ $image->url }}"
                         alt="{{ $image->alt_text }}"
                         class="w-full h-full object-cover min-h-[300px]">
                </div>
            </div>
        @endif
    @endif
@endif

</div>
{{-- End Block Wrapper --}}
