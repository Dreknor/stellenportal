@props(['packages'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Guthaben-Pakete verwalten</h2>
                        <a href="{{ route('credits.packages.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                            Neues Paket erstellen
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($packages->isEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-gray-600 mb-4">Noch keine Guthaben-Pakete vorhanden</p>
                            <a href="{{ route('credits.packages.create') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                Erstes Paket erstellen →
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($packages as $package)
                                <div class="border rounded-lg p-6 {{ $package->is_active ? 'border-gray-200' : 'border-red-200 bg-red-50' }}">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                                        @if(!$package->is_active)
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                                Inaktiv
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                Aktiv
                                            </span>
                                        @endif
                                    </div>

                                    @if($package->description)
                                        <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
                                    @endif

                                    <div class="mb-4">
                                        <div class="text-3xl font-bold text-blue-600 mb-1">
                                            {{ number_format($package->credits, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">Guthaben</div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="text-2xl font-bold text-gray-900 mb-1">
                                            {{ number_format($package->price, 2, ',', '.') }} €
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ number_format($package->pricePerCredit, 4, ',', '.') }} € pro Guthaben
                                        </div>
                                    </div>

                                    <div class="text-xs text-gray-400 mb-4">
                                        Erstellt: {{ $package->created_at->format('d.m.Y') }}
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('credits.packages.edit', $package) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded transition text-sm font-semibold">
                                            Bearbeiten
                                        </a>
                                        <form action="{{ route('credits.packages.destroy', $package) }}" method="POST" class="flex-1" onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Paket löschen möchten?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded transition text-sm font-semibold">
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

