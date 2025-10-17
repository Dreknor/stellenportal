@props([
    'breadcrumbs' => [],
    'showDashboard' => true,
])

@php
    // Normalize breadcrumb input: allow callers to pass strings or arrays.
    $items = is_array($breadcrumbs) ? $breadcrumbs : [$breadcrumbs];
@endphp

<div class="mb-6 flex items-center text-sm">
    @if($showDashboard)
        <a href="{{ route('dashboard') }}"
           class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>

        @if(count($items) > 0)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        @endif
    @endif

    @if(count($items) > 0)
        @foreach($items as $item)
            @php
                // Support either a string (label) or an array with 'label' and optional 'url'
                if (is_string($item)) {
                    $label = $item;
                    $url = null;
                } else {
                    $label = $item['label'] ?? ($item['title'] ?? '');
                    $url = $item['url'] ?? null;
                }
            @endphp

            @if(isset($url) && $url)
                <a href="{{ $url }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __($label) }}</a>
            @else
                {{-- If it's the last item (current page) render as plain text style --}}
                <span class="text-gray-500 dark:text-gray-400">{{ __($label) }}</span>
            @endif

            {{-- Render separator unless this is the last item --}}
            @if(! $loop->last)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif
        @endforeach
    @else
        {{-- No breadcrumbs provided: allow callers to supply a custom slot instead --}}
        {{ $slot }}
    @endif
</div>
