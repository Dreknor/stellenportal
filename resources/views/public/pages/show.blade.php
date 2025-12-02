<x-layouts.public>
    <x-slot name="title">{{ $page->meta_title ?? $page->title }}</x-slot>
    <x-slot name="metaDescription">{{ $page->meta_description }}</x-slot>

    <div class="container mx-auto px-4 py-8">
        <article class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
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
            <div class="prose dark:prose-invert lg:prose-lg max-w-none">
                {!! $page->content !!}
            </div>

            <!-- Content Blocks -->
            @if($page->contentBlocks->where('is_visible', true)->count() > 0)
                <div class="mt-8">
                    @foreach($page->contentBlocks->where('is_visible', true) as $block)
                        @include('public.pages.partials.content-block', ['block' => $block, 'page' => $page])
                    @endforeach
                </div>
            @endif

            <!-- Page Images -->
            @if($page->images->count() > 0)
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($page->images as $image)
                        <figure class="rounded-lg overflow-hidden shadow-md">
                            <img src="{{ $image->url }}"
                                 alt="{{ $image->alt_text }}"
                                 class="w-full h-64 object-cover"
                                 @if($image->title) title="{{ $image->title }}" @endif>
                            @if($image->alt_text)
                                <figcaption class="p-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800">
                                    {{ $image->alt_text }}
                                </figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @endif
        </article>
    </div>
</x-layouts.public>

