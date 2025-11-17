@if(session()->has('impersonate_original_user'))
    @php
        $originalUser = \App\Models\User::find(session('impersonate_original_user'));
    @endphp
    <div class="bg-yellow-500 text-white px-6 py-3 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold">
                        Impersonierung aktiv
                    </p>
                    <p class="text-sm">
                        Sie sind angemeldet als <strong>{{ Auth::user()->name }}</strong>.
                        Original-Benutzer: <strong>{{ $originalUser->name }}</strong>
                    </p>
                </div>
            </div>
            <form action="{{ route('admin.impersonate.stop') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white text-yellow-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Impersonierung beenden
                </button>
            </form>
        </div>
    </div>
@endif

