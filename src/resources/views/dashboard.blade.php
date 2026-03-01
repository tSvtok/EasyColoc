<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Welcome card --}}
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-100">Bienvenue, {{ $user->name }} !</h3>
                    <p class="text-gray-400 mt-1">
                        Réputation : <span class="font-bold {{ $user->reputation >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $user->reputation >= 0 ? '+' : '' }}{{ $user->reputation }}</span>
                        @if($user->isAdmin())
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-900 text-purple-200">Admin</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Pending invitations --}}
            @if($pendingInvitations->count() > 0)
                <div class="bg-yellow-900/30 border border-yellow-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-yellow-300 mb-4">Invitations en attente</h3>
                        @foreach($pendingInvitations as $invitation)
                            <div class="flex items-center justify-between bg-gray-800 p-4 rounded-lg mb-2 border border-gray-700">
                                <div>
                                    <p class="font-medium text-gray-100">{{ $invitation->colocation->name }}</p>
                                    <p class="text-sm text-gray-400">Invitation reçue le {{ $invitation->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                                            Accepter
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                                            Refuser
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Active colocation or create --}}
            @if($colocation)
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-100">{{ $colocation->name }}</h3>
                                <p class="text-sm text-gray-400">{{ $colocation->description }}</p>
                            </div>
                            <a href="{{ route('colocations.show', $colocation) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 text-sm font-medium">
                                Voir ma colocation
                            </a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div class="bg-indigo-900/50 rounded-lg p-4 text-center border border-indigo-800">
                                <p class="text-2xl font-bold text-indigo-400">{{ $colocation->activeMembers()->count() }}</p>
                                <p class="text-sm text-gray-400">Membres</p>
                            </div>
                            <div class="bg-green-900/50 rounded-lg p-4 text-center border border-green-800">
                                <p class="text-2xl font-bold text-green-400">{{ number_format($colocation->expenses()->sum('amount'), 2) }} Dh</p>
                                <p class="text-sm text-gray-400">Total dépenses</p>
                            </div>
                            <div class="bg-purple-900/50 rounded-lg p-4 text-center border border-purple-800">
                                <p class="text-2xl font-bold text-purple-400">{{ $colocation->expenses()->count() }}</p>
                                <p class="text-sm text-gray-400">Dépenses</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                    <div class="p-6 text-center">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-100 mb-2">Pas de colocation active</h3>
                        <p class="text-gray-400 mb-6">Créez une colocation ou attendez une invitation pour commencer.</p>
                        <a href="{{ route('colocations.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 font-medium">
                            Créer une colocation
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
