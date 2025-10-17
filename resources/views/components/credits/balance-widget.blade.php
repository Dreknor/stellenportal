@props(['creditable'])

@php
    $balance = $creditable->getCurrentCreditBalance();
    $isOrganization = $creditable instanceof \App\Models\Organization;
    $colorClass = $isOrganization ? 'green' : 'blue';
@endphp

<div class="bg-{{ $colorClass }}-50 border border-{{ $colorClass }}-200 rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-2 mb-1">
                <svg class="w-5 h-5 text-{{ $colorClass }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-700">Guthaben-Kontostand</h3>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-bold text-{{ $colorClass }}-600">{{ number_format($balance, 0, ',', '.') }}</p>
                <span class="text-sm text-gray-500">Credits</span>
            </div>
        </div>
        <div class="flex flex-col space-y-2">
            @if($isOrganization)
                @can('purchaseCredits', $creditable)
                    <a href="{{ route('credits.organization.purchase', $creditable) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Aufladen
                    </a>
                @endcan
                @can('transferCredits', $creditable)
                    <a href="{{ route('credits.organization.transfer', $creditable) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Umbuchen
                    </a>
                @endcan
                @can('viewTransactions', $creditable)
                    <a href="{{ route('credits.organization.transactions', $creditable) }}" class="text-sm text-green-600 hover:text-green-800 text-center">
                        Historie →
                    </a>
                @endcan
            @else
                @can('purchaseCredits', $creditable)
                    <a href="{{ route('credits.facility.purchase', $creditable) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Aufladen
                    </a>
                @endcan
                @can('viewTransactions', $creditable)
                    <a href="{{ route('credits.facility.transactions', $creditable) }}" class="text-sm text-blue-600 hover:text-blue-800 text-center">
                        Historie →
                    </a>
                @endcan
            @endif
        </div>
    </div>

    @if($balance < 10)
        <div class="mt-3 pt-3 border-t border-{{ $colorClass }}-300">
            <p class="text-sm text-red-600 font-semibold">
                ⚠️ Niedriger Kontostand - Bitte laden Sie Guthaben auf
            </p>
        </div>
    @endif
</div>
