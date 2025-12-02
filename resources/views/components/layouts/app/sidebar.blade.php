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



                            <!-- Admin Bereich -->
                            @canany(['admin view users', 'admin view organizations', 'admin view facilities', 'admin view job postings', 'admin view credits', 'admin view logs'])
                            <li class="pt-4 pb-2">
                                <div x-show="sidebarOpen" class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Administration
                                </div>
                                <div x-show="!sidebarOpen" class="border-t border-gray-300 dark:border-gray-600 mx-2"></div>
                            </li>

                            @can('admin view users')
                            <x-layouts.sidebar-link href="{{ route('admin.dashboard') }}" icon='fas-gauge-high'
                                :active="request()->routeIs('admin.dashboard')">Admin Dashboard</x-layouts.sidebar-link>
                            @endcan

                            <x-layouts.sidebar-two-level-link-parent title="Admin" icon="fas-user-shield"
                                :active="request()->routeIs('admin.users*') || request()->routeIs('admin.organizations*') || request()->routeIs('admin.facilities*') || request()->routeIs('admin.job-postings*') || request()->routeIs('admin.credits*') || request()->routeIs('admin.audits*')">

                                @can('admin view users')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.users.index') }}" icon='fas-users'
                                    :active="request()->routeIs('admin.users*')">Benutzer</x-layouts.sidebar-two-level-link>
                                @endcan

                                @can('admin view organizations')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.organizations.index') }}" icon='fas-building'
                                    :active="request()->routeIs('admin.organizations*')">Organisationen</x-layouts.sidebar-two-level-link>
                                @endcan

                                @can('admin view facilities')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.facilities.index') }}" icon='fas-school'
                                    :active="request()->routeIs('admin.facilities*')">Einrichtungen</x-layouts.sidebar-two-level-link>
                                @endcan

                                @can('admin view job postings')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.job-postings.index') }}" icon='fas-briefcase'
                                    :active="request()->routeIs('admin.job-postings*')">Stellenausschreibungen</x-layouts.sidebar-two-level-link>
                                @endcan

                                @can('admin view credits')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.credits.index') }}" icon='fas-coins'
                                    :active="request()->routeIs('admin.credits.index') || request()->routeIs('admin.credits.transactions') || request()->routeIs('admin.credits.grant')">Guthaben</x-layouts.sidebar-two-level-link>
                                @endcan

                                @can('admin view credits')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.job-posting-credit-exemptions.index') }}" icon='fas-file-circle-exclamation'
                                    :active="request()->routeIs('admin.job-posting-credit-exemptions*')">Guthabenausnahmen</x-layouts.sidebar-two-level-link>
                                @endcan

                                @can('admin view logs')
                                <x-layouts.sidebar-two-level-link href="{{ route('admin.audits.index') }}" icon='fas-list-check'
                                    :active="request()->routeIs('admin.audits*')">Audit Logs</x-layouts.sidebar-two-level-link>
                                @endcan
                            </x-layouts.sidebar-two-level-link-parent>
                            @endcanany

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


                            <!-- CMS Bereich -->
            @canany(['admin view pages', 'admin manage menus'])
            <li class="pt-4 pb-2">
                <div x-show="sidebarOpen" class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    CMS
                </div>
                <div x-show="!sidebarOpen" class="border-t border-gray-300 dark:border-gray-600 mx-2"></div>
            </li>

            <x-layouts.sidebar-two-level-link-parent title="Content Management" icon="fas-file-lines"
                :active="request()->routeIs('cms.pages*') || request()->routeIs('cms.menus*')">

                @can('admin view pages')
                <x-layouts.sidebar-two-level-link href="{{ route('cms.pages.index') }}" icon='fas-file-lines'
                    :active="request()->routeIs('cms.pages*')">Seiten</x-layouts.sidebar-two-level-link>
                @endcan

                @can('admin manage menus')
                <x-layouts.sidebar-two-level-link href="{{ route('cms.menus.index') }}" icon='fas-bars'
                    :active="request()->routeIs('cms.menus*')">Menüs</x-layouts.sidebar-two-level-link>
                @endcan
            </x-layouts.sidebar-two-level-link-parent>
            @endcanany


                            <!-- Hilfe -->
                            <li class="pt-4 pb-2">
                                <div x-show="sidebarOpen" class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Support
                                </div>
                                <div x-show="!sidebarOpen" class="border-t border-gray-300 dark:border-gray-600 mx-2"></div>
                            </li>

                            <x-layouts.sidebar-link href="{{ route('help') }}" icon='fas-circle-question'
                                :active="request()->routeIs('help')">Hilfe & FAQ</x-layouts.sidebar-link>
                        </ul>
                    </nav>
                </div>
            </aside>
