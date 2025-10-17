<aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
                class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <!-- Dashboard -->
                            <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                                :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>

                            <x-layouts.sidebar-link href="{{ route('organizations.index') }}" icon='fas-building'
                                :active="request()->routeIs('organizations*')">Träger</x-layouts.sidebar-link>

                            <x-layouts.sidebar-link href="{{ route('facilities.index') }}" icon="fas-school"
                                :active="request()->routeIs('facilities*')">Einrichtungen</x-layouts.sidebar-link>

                            <!-- Stellenausschreibungen -->
                            <x-layouts.sidebar-link href="{{ route('job-postings.index') }}" icon='fas-briefcase'
                                :active="request()->routeIs('job-postings*')">Stellenausschreibungen</x-layouts.sidebar-link>

                            <!-- Guthaben-System -->
                            @can('manage credit packages')
                            <x-layouts.sidebar-link href="{{ route('credits.packages.index') }}" icon='fas-coins'
                                :active="request()->routeIs('credits.packages*')">Guthaben-Pakete</x-layouts.sidebar-link>
                            @endcan

                            <!-- Rollen & Berechtigungen -->
                            @can('view roles')
                            <x-layouts.sidebar-two-level-link-parent title="Berechtigungen" icon="fas-shield-halved"
                                :active="request()->routeIs('roles*') || request()->routeIs('permissions*')">
                                @can('view roles')
                                <x-layouts.sidebar-two-level-link href="{{ route('roles.index') }}" icon='fas-user-tag'
                                    :active="request()->routeIs('roles*')">Rollen</x-layouts.sidebar-two-level-link>
                                @endcan
                                @can('view permissions')
                                <x-layouts.sidebar-two-level-link href="{{ route('permissions.index') }}" icon='fas-key'
                                    :active="request()->routeIs('permissions*')">Rechte</x-layouts.sidebar-two-level-link>
                                @endcan
                            </x-layouts.sidebar-two-level-link-parent>
                            @endcan
                        </ul>
                    </nav>
                </div>
            </aside>
