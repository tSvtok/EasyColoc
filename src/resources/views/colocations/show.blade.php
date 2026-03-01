<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ $colocation->name }}</h2>
            @if($isOwner)
                <div class="flex space-x-2">
                    <a href="{{ route('colocations.edit', $colocation) }}" class="bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 text-sm">Modifier</a>
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette colocation ?')">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 text-sm">Annuler la colocation</button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('colocations.leave', $colocation) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir quitter cette colocation ?')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 text-sm">Quitter</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            @if($colocation->description)
                <div class="bg-gray-800 shadow-sm sm:rounded-lg p-4 border border-gray-700">
                    <p class="text-gray-400">{{ $colocation->description }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left column: Members + Categories --}}
                <div class="space-y-6">

                    {{-- Members --}}
                    <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4">Membres ({{ $members->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($members as $member)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-100">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $member->pivot->role === 'owner' ? 'Propriétaire' : 'Membre' }}
                                            &middot; Réputation: <span class="{{ $member->reputation >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $member->reputation >= 0 ? '+' : '' }}{{ $member->reputation }}</span>
                                        </p>
                                    </div>
                                    @if($isOwner && $member->pivot->role !== 'owner')
                                        <form method="POST" action="{{ route('colocations.removeMember', [$colocation, $member->id]) }}" onsubmit="return confirm('Retirer {{ $member->name }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Retirer</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Invite form (owner only) --}}
                        @if($isOwner)
                            <div class="mt-6 pt-4 border-t border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-300 mb-2">Inviter un membre</h4>
                                <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="flex space-x-2">
                                    @csrf
                                    <input type="email" name="email" placeholder="email@exemple.com" required class="flex-1 bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm">Inviter</button>
                                </form>
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                        @endif
                    </div>

                    {{-- Categories (owner only) --}}
                    @if($isOwner)
                        <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-100 mb-4">Catégories</h3>
                            <div class="space-y-2 mb-4">
                                @foreach($categories as $category)
                                    <div class="flex items-center justify-between bg-gray-700/50 px-3 py-2 rounded">
                                        <span class="text-sm text-gray-300">{{ $category->name }}</span>
                                        <form method="POST" action="{{ route('categories.destroy', [$colocation, $category]) }}" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Supprimer</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('categories.store', $colocation) }}" class="flex space-x-2">
                                @csrf
                                <input type="text" name="name" placeholder="Nouvelle catégorie" required maxlength="255" class="flex-1 bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 text-sm">Ajouter</button>
                            </form>
                        </div>
                    @endif

                    {{-- Balances --}}
                    <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4">Soldes</h3>
                        <div class="space-y-2">
                            @foreach($members as $member)
                                @php $balance = $balances[$member->id] ?? 0; @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ $member->name }}</span>
                                    <span class="font-semibold text-sm {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance, 2) }} Dh
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Middle column: Expenses --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Add expense form --}}
                    <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4">Ajouter une dépense</h3>
                        <form method="POST" action="{{ route('expenses.store', $colocation) }}">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="title" value="Titre" />
                                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required maxlength="255" :value="old('title')" />
                                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="amount" value="Montant (Dh)" />
                                    <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required :value="old('amount')" />
                                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="expense_date" value="Date" />
                                    <x-text-input id="expense_date" name="expense_date" type="date" class="mt-1 block w-full" required :value="old('expense_date', date('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('expense_date')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="category_id" value="Catégorie" />
                                    <select id="category_id" name="category_id" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-100 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Aucune --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="paid_by" value="Payé par" />
                                    <select id="paid_by" name="paid_by" required class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-100 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ old('paid_by', auth()->id()) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <x-primary-button class="w-full justify-center">Ajouter la dépense</x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Expenses list with month filter --}}
                    <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-100">Dépenses</h3>
                            <form method="GET" action="{{ route('colocations.show', $colocation) }}" class="flex items-center space-x-2">
                                <select name="month" onchange="this.form.submit()" class="bg-gray-700 border-gray-600 text-gray-100 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tous les mois</option>
                                    @foreach($availableMonths as $m)
                                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        @if($expenses->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-700/50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Titre</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Catégorie</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Payé par</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                                        @foreach($expenses as $expense)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-400">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-100">{{ $expense->title }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-400">{{ $expense->category?->name ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-400">{{ $expense->payer->name }}</td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-100">{{ number_format($expense->amount, 2) }} Dh</td>
                                                <td class="px-4 py-3 text-sm text-right">
                                                    @if($isOwner || (int)$expense->paid_by === (int)auth()->id())
                                                        <form method="POST" action="{{ route('expenses.destroy', [$colocation, $expense]) }}" onsubmit="return confirm('Supprimer cette dépense ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Supprimer</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-400 text-center py-8">Aucune dépense enregistrée.</p>
                        @endif
                    </div>

                    {{-- Settlements (who owes whom) --}}
                    <div class="bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-100">Remboursements</h3>
                            @if($isOwner)
                                <form method="POST" action="{{ route('settlements.recalculate', $colocation) }}">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 text-sm">Recalculer</button>
                                </form>
                            @endif
                        </div>

                        {{-- Calculated settlements view --}}
                        @if(count($settlements) > 0)
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-400 mb-3">Qui doit à qui</h4>
                                <div class="space-y-2">
                                    @foreach($settlements as $s)
                                        <div class="flex items-center justify-between bg-yellow-900/30 p-3 rounded-lg border border-yellow-800">
                                            <span class="text-sm">
                                                <span class="font-medium text-red-600">{{ $s['from']->name }}</span>
                                                doit
                                                <span class="font-bold">{{ number_format($s['amount'], 2) }} Dh</span>
                                                à
                                                <span class="font-medium text-green-600">{{ $s['to']->name }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400 text-sm mb-4">Aucun remboursement nécessaire.</p>
                        @endif

                        {{-- Saved settlements (payments) --}}
                        @if($payments->count() > 0)
                            <h4 class="text-sm font-semibold text-gray-400 mb-3">Paiements enregistrés</h4>
                            <div class="space-y-2">
                                @foreach($payments as $payment)
                                    <div class="flex items-center justify-between bg-gray-700/50 p-3 rounded-lg border border-gray-700">
                                        <span class="text-sm">
                                            {{ $payment->fromUser->name }} → {{ $payment->toUser->name }}:
                                            <span class="font-bold">{{ number_format($payment->amount, 2) }} Dh</span>
                                        </span>
                                        <div class="flex items-center space-x-2">
                                            @if($payment->is_paid)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900 text-green-200">Payé</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-900 text-orange-200">En attente</span>
                                                @if((int)$payment->to_user_id === (int)auth()->id() || $isOwner)
                                                    <form method="POST" action="{{ route('settlements.markPaid', [$colocation, $payment]) }}">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">Marquer payé</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
