@php
    $menuItemUrl = function ($item) {
        return ($item->page_id && $item->page) ? route('pages.show', $item->page->slug) : $item->url;
    };
    $navLinkClasses = 'px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors';
    $mobileLinkClasses = 'block px-3 py-3 rounded-lg text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-gray-700 transition-colors';
@endphp

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[60] focus:bg-white focus:text-primary-800 focus:px-4 focus:py-2 focus:rounded-lg">{{ __('Zum Inhalt springen') }}</a>

    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-gray-800/95 backdrop-blur border-b border-sand-200 dark:border-gray-700">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="{{ __('Hauptnavigation') }}">
            <div class="flex justify-between items-center h-16 lg:h-[72px] gap-4">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
                    <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-10 w-auto">
                    <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100 hidden sm:inline">{{ config('app.name') }}</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-1 flex-1 justify-end" x-data="{ openDropdown: null }">
                    <a href="{{ route('public.jobs.index') }}" @class([$navLinkClasses, 'text-primary-700 dark:text-primary-300' => request()->routeIs('public.jobs.*')]) @if(request()->routeIs('public.jobs.*')) aria-current="page" @endif>
                        Stellenangebote
                    </a>
                    <a href="{{ route('public.pricing') }}" @class([$navLinkClasses, 'text-primary-700 dark:text-primary-300' => request()->routeIs('public.pricing')]) @if(request()->routeIs('public.pricing')) aria-current="page" @endif>
                        Preise
                    </a>

                    @if(isset($headerMenu) && $headerMenu->count() > 0)
                        @foreach($headerMenu as $menuItem)
                            @if($menuItem->children->count() > 0)
                                <div class="relative" @click.away="openDropdown = null" @keydown.escape="openDropdown = null">
                                    <button type="button" @click="openDropdown = openDropdown === {{ $menuItem->id }} ? null : {{ $menuItem->id }}"
                                            class="{{ $navLinkClasses }} flex items-center gap-1"
                                            :aria-expanded="openDropdown === {{ $menuItem->id }}">
                                        {{ $menuItem->label }}
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openDropdown === {{ $menuItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openDropdown === {{ $menuItem->id }}"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white dark:bg-gray-800 border border-sand-200 dark:border-gray-700 py-2 z-50"
                                         style="display: none;">
                                        @foreach($menuItem->children as $child)
                                            @if($child->is_active && $menuItemUrl($child))
                                                <a href="{{ $menuItemUrl($child) }}" target="{{ $child->target }}"
                                                   class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-gray-700 hover:text-primary-700">
                                                    {{ $child->label }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($menuItemUrl($menuItem))
                                <a href="{{ $menuItemUrl($menuItem) }}" target="{{ $menuItem->target }}" class="{{ $navLinkClasses }}">
                                    {{ $menuItem->label }}
                                </a>
                            @endif
                        @endforeach
                    @endif

                    <span class="w-px h-6 bg-sand-200 dark:bg-gray-700 mx-2" aria-hidden="true"></span>

                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-5 py-2.5 text-white rounded-lg font-semibold">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="{{ $navLinkClasses }}">
                            Anmelden
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 text-white rounded-lg font-semibold">
                                Stelle ausschreiben
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-sand-100 dark:hover:bg-gray-700" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                    <span class="sr-only">Hauptmenü öffnen</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" id="menu-icon-closed">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" id="menu-icon-open">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="lg:hidden hidden border-t border-sand-200 dark:border-gray-700 bg-white dark:bg-gray-800" id="mobile-menu" x-data="{ openMobileDropdown: null }">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('public.jobs.index') }}" class="{{ $mobileLinkClasses }}">Stellenangebote</a>
                <a href="{{ route('public.pricing') }}" class="{{ $mobileLinkClasses }}">Preise</a>

                @if(isset($headerMenu) && $headerMenu->count() > 0)
                    @foreach($headerMenu as $menuItem)
                        @if($menuItem->children->count() > 0)
                            <div>
                                <button type="button" @click="openMobileDropdown = openMobileDropdown === {{ $menuItem->id }} ? null : {{ $menuItem->id }}"
                                        class="{{ $mobileLinkClasses }} w-full flex items-center justify-between"
                                        :aria-expanded="openMobileDropdown === {{ $menuItem->id }}">
                                    <span>{{ $menuItem->label }}</span>
                                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': openMobileDropdown === {{ $menuItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openMobileDropdown === {{ $menuItem->id }}" class="pl-4 space-y-1" style="display: none;">
                                    @foreach($menuItem->children as $child)
                                        @if($child->is_active && $menuItemUrl($child))
                                            <a href="{{ $menuItemUrl($child) }}" target="{{ $child->target }}" class="{{ $mobileLinkClasses }} text-sm">
                                                {{ $child->label }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @elseif($menuItemUrl($menuItem))
                            <a href="{{ $menuItemUrl($menuItem) }}" target="{{ $menuItem->target }}" class="{{ $mobileLinkClasses }}">
                                {{ $menuItem->label }}
                            </a>
                        @endif
                    @endforeach
                @endif

                <div class="pt-3 mt-2 border-t border-sand-200 dark:border-gray-700 space-y-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block px-6 py-3 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="{{ $mobileLinkClasses }}">Anmelden</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block px-6 py-3 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700">Stelle ausschreiben</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');
        const iconClosed = document.getElementById('menu-icon-closed');
        const iconOpen = document.getElementById('menu-icon-open');

        if (!button || !menu) {
            return;
        }

        const setOpen = (open) => {
            menu.classList.toggle('hidden', !open);
            iconClosed?.classList.toggle('hidden', open);
            iconOpen?.classList.toggle('hidden', !open);
            button.setAttribute('aria-expanded', String(open));
        };

        button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));
        menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                setOpen(false);
                button.focus();
            }
        });
    });
</script>
