<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Modifier la colocation</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6">
                    <form method="POST" action="{{ route('colocations.update', $colocation) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" value="Nom de la colocation" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required maxlength="255" :value="old('name', $colocation->name)" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="description" value="Description (optionnel)" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" maxlength="1000">{{ old('description', $colocation->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('colocations.show', $colocation) }}" class="text-gray-400 hover:text-gray-200">Annuler</a>
                            <x-primary-button>Mettre à jour</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
