<!-- Header -->
<header class="bg-white dark:bg-gray-800 z-20 border-b border-sand-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4">
        <!-- Left side: Logo and toggle -->
        <div class="flex items-center">
            <button type="button" @click="toggleSidebar" :aria-expanded="sidebarOpen.toString()" aria-label="Navigation ein-/ausblenden"
                class="w-11 h-11 inline-flex items-center justify-center rounded-lg text-gray-600 hover:bg-sand-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ url('/') }}" class="ml-3 flex items-center gap-3" title="{{ config('app.name') }} - Startseite">
                <img src="{{ asset('img/Stellenportal-Logo.png') }}" alt="" class="h-9 w-auto">
                <span class="text-lg font-bold text-gray-900 dark:text-gray-100 hidden sm:inline">{{ config('app.name') }}</span>
            </a>

        </div>

        <!-- Right side: Search, notifications, profile -->
        <div class="flex items-center space-x-4">
            <!-- Profile -->
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" class="flex items-center gap-1 rounded-lg p-1 hover:bg-sand-100 dark:hover:bg-gray-700">
                    <span class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full">
                        <span
                            class="flex h-full w-full items-center justify-center rounded-full bg-primary-800 text-white text-sm font-bold">
                            {{ Auth::user()->initials() }}
                        </span>
                    </span>
                    <span class="ml-2 hidden md:block">{{ Auth::user()->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" :class="{ 'block': open, 'hidden': !open }"
                    class="hidden absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-xl shadow-lg py-1 z-50 border border-sand-200 dark:border-gray-700">
                    <a href="{{ route('settings.profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Einstellungen
                        </div>
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Abmelden
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
