<x-layouts.app>
    @php
        $crumbs = [
            ['label' =>  __('Einrichtungen'), 'url' => route('facilities.index')],
            ['label' =>  $facility->name, 'url' => route('facilities.show', $facility)],
            ['label' =>  __('Benutzer')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Benutzer verwalten') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $facility->name }}</p>
    </div>

    <!-- Existing Users List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 pb-0">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Bestehende Benutzer') }}</h2>

            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th scope="col" class="px-6 p-5 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('E-Mail') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Aktionen') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 flex items-center justify-center text-sm font-semibold">
                                                    {{ $user->initials() }}
                                                </div>
                                            </div>
                                            <div class="ml-4 text-center">
                                                <div class="text-sm text-center font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <form action="{{ route('facilities.users.destroy', [$facility, $user]) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    onclick="return confirm('{{ __('Sind Sie sicher, dass Sie diesen Benutzer entfernen möchten?') }}')">
                                                {{ __('Entfernen') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('Noch keine Benutzer vorhanden.') }}</p>
            @endif
        </div>
    </div>

    <!-- Add New User Section -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Neuen Benutzer anlegen') }}</h2>

            <x-forms.user-form
                :action="route('facilities.users.store', $facility)"
                method="POST"
            />
        </div>
    </div>
</x-layouts.app>

