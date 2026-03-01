<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Administration</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <p class="text-3xl font-bold text-indigo-400">{{ $totalUsers }}</p>
                    <p class="text-sm text-gray-400 mt-1">Utilisateurs</p>
                </div>
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <p class="text-3xl font-bold text-green-400">{{ $activeColocations }}</p>
                    <p class="text-sm text-gray-400 mt-1">Colocations actives</p>
                </div>
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <p class="text-3xl font-bold text-purple-400">{{ $totalExpenses }}</p>
                    <p class="text-sm text-gray-400 mt-1">Dépenses totales</p>
                </div>
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <p class="text-3xl font-bold text-orange-400">{{ number_format($totalExpenseAmount, 2) }} Dh</p>
                    <p class="text-sm text-gray-400 mt-1">Montant total</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <p class="text-3xl font-bold text-gray-300">{{ $totalColocations }}</p>
                    <p class="text-sm text-gray-400 mt-1">Colocations totales</p>
                </div>
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <p class="text-3xl font-bold text-red-400">{{ $bannedUsers }}</p>
                    <p class="text-sm text-gray-400 mt-1">Utilisateurs bannis</p>
                </div>
            </div>

            {{-- Users table --}}
            <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-gray-100 mb-4">Gestion des utilisateurs</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Nom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Rôle</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Réputation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Inscrit le</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $user->id }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-100">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($user->isAdmin())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-900 text-purple-200">Admin</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-700 text-gray-300">User</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm {{ $user->reputation >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $user->reputation >= 0 ? '+' : '' }}{{ $user->reputation }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($user->isBanned())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-900 text-red-200">Banni</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900 text-green-200">Actif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        @if(!$user->isAdmin())
                                            @if($user->isBanned())
                                                <form method="POST" action="{{ route('admin.unban', $user) }}">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm">Débannir</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.ban', $user) }}" onsubmit="return confirm('Bannir {{ $user->name }} ?')">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Bannir</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
