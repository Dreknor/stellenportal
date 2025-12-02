@php
    $footerSetting = \App\Models\FooterSetting::getActive();
@endphp

@if($footerSetting)
    <footer class="border-t mt-12" role="contentinfo"
            style="background-color: {{ $footerSetting->background_color }}; color: {{ $footerSetting->text_color }};">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Logo Section -->
                @if($footerSetting->logo_path)
                    <div class="flex justify-center md:justify-start items-center">
                        <img src="{{ $footerSetting->logo_url }}" alt="{{ config('app.name') }} Logo" class="h-16 w-auto">
                    </div>
                @endif

                <!-- Content Section -->
                @if($footerSetting->content)
                    <div class="text-center md:text-left" style="color: {{ $footerSetting->text_color }};">
                        {!! nl2br(e($footerSetting->content)) !!}
                    </div>
                @endif

                <!-- Links Section -->
                @if($footerSetting->links && count($footerSetting->links) > 0)
                    <div class="flex flex-col items-center md:items-end space-y-2">
                        @foreach($footerSetting->links as $link)
                            <a href="{{ $link['url'] }}"
                               class="hover:underline"
                               style="color: {{ $footerSetting->link_color }};"
                               target="_blank"
                               rel="noopener noreferrer">
                                {{ $link['title'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- CMS Footer Menu -->
            @if(isset($footerMenu) && $footerMenu->count() > 0)
                <div class="mt-8 pt-6 border-t" style="border-color: {{ $footerSetting->text_color }}33;">
                    <nav class="flex flex-wrap justify-center gap-4" aria-label="Footer Navigation">
                        @foreach($footerMenu as $menuItem)
                            @if($menuItem->is_active)
                                @php
                                    $menuUrl = $menuItem->url;
                                    if ($menuItem->page_id && $menuItem->page) {
                                        $menuUrl = route('pages.show', $menuItem->page->slug);
                                    }
                                @endphp
                                @if($menuUrl)
                                    <a href="{{ $menuUrl }}"
                                       target="{{ $menuItem->target }}"
                                       class="hover:underline text-sm"
                                       style="color: {{ $footerSetting->link_color }};">
                                        {{ $menuItem->label }}
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    </nav>
                </div>
            @endif
        </div>
    </footer>
@else
    <!-- Fallback Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12" role="contentinfo">
        <div class="container mx-auto px-4 py-8">
            <!-- CMS Footer Menu -->
            @if(isset($footerMenu) && $footerMenu->count() > 0)
                <nav class="flex flex-wrap justify-center gap-4 mb-6" aria-label="Footer Navigation">
                    @foreach($footerMenu as $menuItem)
                        @if($menuItem->is_active)
                            @php
                                $menuUrl = $menuItem->url;
                                if ($menuItem->page_id && $menuItem->page) {
                                    $menuUrl = route('pages.show', $menuItem->page->slug);
                                }
                            @endphp
                            @if($menuUrl)
                                <a href="{{ $menuUrl }}"
                                   target="{{ $menuItem->target }}"
                                   class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm transition-colors">
                                    {{ $menuItem->label }}
                                </a>
                            @endif
                        @endif
                    @endforeach
                </nav>
            @endif

            <div class="text-center text-gray-600 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Alle Rechte vorbehalten.') }}</p>
            </div>
        </div>
    </footer>
@endif

