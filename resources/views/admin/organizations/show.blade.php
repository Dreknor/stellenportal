<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Organisationen'), 'url' => route('admin.organizations.index')],
            ['label' => $organization->name],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $organization->name }}</h1>
        @can('admin edit organizations')
            <x-button type="primary" tag="a" :href="route('admin.organizations.edit', $organization)">
                <x-fas-edit class="w-3 mr-3"/>
                {{ __('Bearbeiten') }}
            </x-button>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Organization Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Organisationsinformationen') }}</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $organization->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Slug') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $organization->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('E-Mail') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $organization->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Telefon') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $organization->phone ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Website') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if($organization->website)
                                <a href="{{ $organization->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $organization->website }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    @if($organization->description)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Beschreibung') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $organization->description }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Erstellt am') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $organization->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aktualisiert am') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $organization->updated_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Images -->
            <x-media-display :model="$organization" />

            <!-- Address -->
            @if($organization->address)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Adresse') }}</h2>
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        <p>{{ $organization->address->street }} {{ $organization->address->street_number }}</p>
                        <p>{{ $organization->address->postal_code }} {{ $organization->address->city }}</p>
                        <p>{{ $organization->address->country }}</p>
                    </div>
                </div>
            @endif

            <!-- Facilities -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Einrichtungen') }} ({{ $organization->facilities->count() }})</h2>
                @forelse($organization->facilities as $facility)
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $facility->name }}</h3>
                            @if($facility->address)
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $facility->address->city }}</p>
                            @endif
                        </div>
                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.facilities.show', $facility)">
                            <x-fas-arrow-right class="w-3"/>
                        </x-button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Einrichtungen vorhanden') }}</p>
                @endforelse
            </div>

            <!-- Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Zugeordnete Benutzer') }} ({{ $organization->users->count() }})</h2>
                @forelse($organization->users as $user)
                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <x-button type="secondary" size="sm" tag="a" :href="route('admin.users.show', $user)">
                            <x-fas-arrow-right class="w-3"/>
                        </x-button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keine Benutzer zugeordnet') }}</p>
                @endforelse
            </div>

            <!-- Credit Transactions -->
            @if($organization->creditTransactions->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Letzte Transaktionen') }}</h2>
                    <div class="space-y-2">
                        @foreach($organization->creditTransactions->take(5) as $transaction)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="flex-1">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $transaction->description }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                                <div class="text-sm font-bold {{ $transaction->amount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction->amount >= 0 ? '+' : '' }}{{ $transaction->amount }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Approval Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Bestätigungsstatus') }}</h2>
                @if($organization->is_approved)
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 w-full justify-center">
                            <x-fas-check-circle class="w-4 h-4 mr-2"/>
                            {{ __('Bestätigt') }}
                        </span>
                    </div>
                    @if($organization->approved_at)
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('Bestätigt am') }}</dt>
                                <dd class="text-gray-900 dark:text-gray-100 font-medium">{{ $organization->approved_at->format('d.m.Y H:i') }}</dd>
                            </div>
                            @if($organization->approvedBy)
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Bestätigt von') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100 font-medium">{{ $organization->approvedBy->first_name }} {{ $organization->approvedBy->last_name }}</dd>
                                </div>
                            @endif
                        </dl>
                    @endif
                    @can('admin edit organizations')
                        <form method="POST" action="{{ route('admin.organizations.unapprove', $organization) }}" class="mt-4">
                            @csrf
                            <x-button type="danger" native-type="submit" class="w-full" onclick="return confirm('{{ __('Möchten Sie die Bestätigung wirklich zurückziehen?') }}')">
                                <x-fas-times-circle class="w-3 mr-2"/>
                                {{ __('Bestätigung zurückziehen') }}
                            </x-button>
                        </form>
                    @endcan
                @else
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 w-full justify-center">
                            <x-fas-clock class="w-4 h-4 mr-2"/>
                            {{ __('Ausstehend') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('Diese Organisation wurde noch nicht bestätigt.') }}</p>
                    @can('admin edit organizations')
                        <form method="POST" action="{{ route('admin.organizations.approve', $organization) }}">
                            @csrf
                            <x-button type="primary" native-type="submit" class="w-full">
                                <x-fas-check class="w-3 mr-2"/>
                                {{ __('Organisation bestätigen') }}
                            </x-button>
                        </form>
                    @endcan
                @endif
            </div>

            <!-- Cooperative Membership -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Genossenschaft') }}</h2>
                @if($organization->is_cooperative_member)
                    <div class="mb-2">
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 w-full justify-center">
                            <x-fas-star class="w-4 h-4 mr-2"/>
                            {{ __('Genossenschaftsmitglied') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Hat Zugang zu speziellen Guthaben-Paketen') }}</p>
                @else
                    <div class="mb-2">
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 w-full justify-center">
                            {{ __('Kein Mitglied') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Standardzugang zu Guthaben-Paketen') }}</p>
                @endif
            </div>

            <!-- Credit Balance -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Guthaben') }}</h2>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $organization->creditBalance->balance ?? 0 }}
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Credits verfügbar') }}</p>
            </div>

            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Statistiken') }}</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Einrichtungen') }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $organization->facilities->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Benutzer') }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $organization->users->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Transaktionen') }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $organization->creditTransactions->count() }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Actions -->
            @can('admin delete organizations')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Aktionen') }}</h2>
                    <form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}" onsubmit="return confirm('{{ __('Sind Sie sicher, dass Sie diese Organisation löschen möchten?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-button type="danger" native-type="submit" class="w-full">
                            <x-fas-trash class="w-3 mr-2"/>
                            {{ __('Organisation löschen') }}
                        </x-button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>

