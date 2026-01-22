<x-layouts.public>
    <x-slot name="title">{{ $page->meta_title ?? $page->title }}</x-slot>
    <x-slot name="metaDescription">{{ $page->meta_description }}</x-slot>

    @php
        $settings = $page->settings ?? [];
        $maxWidth = $settings['max_width'] ?? 'container';
        $backgroundColor = $settings['background_color'] ?? 'transparent';
        $customCss = $settings['custom_css'] ?? '';

        // Map container classes
        $containerClasses = [
            'container' => 'max-w-7xl',
            'container-sm' => 'max-w-3xl',
            'container-md' => 'max-w-5xl',
            'container-lg' => 'max-w-7xl',
            'container-xl' => 'max-w-screen-2xl',
            'full' => 'max-w-full',
        ];

        $containerClass = $containerClasses[$maxWidth] ?? $containerClasses['container'];

        // Prepare background style
        $bgStyle = '';
        if ($backgroundColor && $backgroundColor !== 'transparent') {
            $bgStyle = 'background-color: ' . e($backgroundColor) . ';';
        }
    @endphp

    @if(!empty($customCss))
        @push('styles')
        <style>
            {!! $customCss !!}
        </style>
        @endpush
    @endif

    <div class="w-full" @if($bgStyle) style="{{ $bgStyle }}" @endif>
        <div class="{{ $containerClass }} mx-auto px-4 py-8">
            <article class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                <!-- Page Title -->
                <header class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $page->title }}</h1>
                    @if($page->published_at)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Veröffentlicht am') }} {{ $page->published_at->format('d. F Y') }}
                        </p>
                    @endif
                </header>

                <!-- Page Content -->
                @if($page->content)
                    <div class="prose dark:prose-invert lg:prose-lg max-w-none">
                        {!! $page->content !!}
                    </div>
                @endif

                <!-- Content Blocks -->
                @if($page->contentBlocks->where('is_visible', true)->whereNull('parent_id')->count() > 0)
                    <div class="mt-8">
                        @foreach($page->contentBlocks->where('is_visible', true)->whereNull('parent_id') as $block)
                            @include('public.pages.partials.content-block', ['block' => $block, 'page' => $page])
                        @endforeach
                    </div>
                @endif
            </article>
        </div>
    </div>
</x-layouts.public>

