<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- User Overview Card --}}
            <div class="p-6 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 rounded-full bg-indigo-600 flex items-center justify-center text-3xl font-bold text-white uppercase">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-100">{{ $user->name }}</h3>
                        <p class="text-gray-400 mt-1">{{ $user->email }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-3">
                            @if($user->isAdmin())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-900 text-purple-200 border border-purple-700">Admin</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-300 border border-gray-600">Utilisateur</span>
                            @endif

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->reputation >= 0 ? 'bg-green-900 text-green-200 border border-green-700' : 'bg-red-900 text-red-200 border border-red-700' }}">
                                Reputation: {{ $user->reputation >= 0 ? '+' : '' }}{{ $user->reputation }}
                            </span>

                            @if($activeColocation)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-900 text-indigo-200 border border-indigo-700">
                                    Colocation: {{ $activeColocation->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-400 border border-gray-600">
                                    Aucune colocation active
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-sm text-gray-400">
                        <p>Membre depuis</p>
                        <p class="font-semibold text-gray-300">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
