@props(['facility', 'transactions', 'balance'])

<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Transaktionshistorie</h2>
                        <div class="flex space-x-2">
                                <a href="{{ route('credits.facility.transfer-to-organization', $facility) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                    An Träger übertragen
                                </a>
                            <a href="{{ route('credits.facility.purchase', $facility) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                Guthaben aufladen
                            </a>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Aktueller Kontostand</p>
                                <p class="text-3xl font-bold text-blue-600">{{ number_format($balance, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">{{ $facility->name }}</p>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($transactions->isEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-600">Noch keine Transaktionen vorhanden</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Betrag</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontostand</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benutzer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->created_at->format('d.m.Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($transaction->type)
                                                    @case('purchase')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Kauf
                                                        </span>
                                                        @break
                                                    @case('transfer_in')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            Eingang
                                                        </span>
                                                        @break
                                                    @case('transfer_out')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                            Ausgang
                                                        </span>
                                                        @break
                                                    @case('usage')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Verwendung
                                                        </span>
                                                        @break
                                                    @case('adjustment')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                            Anpassung
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <span class="{{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($transaction->balance_after, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->user->first_name }} {{ $transaction->user->last_name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @if($transaction->creditPackage)
                                                    <div>{{ $transaction->creditPackage->name }}</div>
                                                    @if($transaction->price_paid)
                                                        <div class="text-xs">{{ number_format($transaction->price_paid, 2, ',', '.') }} €</div>
                                                    @endif
                                                @endif
                                                @if($transaction->relatedCreditable)
                                                    <div class="text-xs">
                                                        {{ $transaction->type === 'transfer_in' ? 'von' : 'an' }}: {{ $transaction->relatedCreditable->name }}
                                                    </div>
                                                @endif
                                                @if($transaction->note)
                                                    <div class="text-xs text-gray-400 mt-1">{{ $transaction->note }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

