<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-100">Invitation à rejoindre</h2>
        <p class="mt-2 text-lg text-indigo-400 font-semibold">{{ $colocation->name }}</p>
    </div>

    @if($colocation->description)
        <p class="text-gray-400 text-center mb-6">{{ $colocation->description }}</p>
    @endif

    <p class="text-sm text-gray-400 text-center mb-6">
        Vous avez été invité à rejoindre cette colocation. Connectez-vous ou inscrivez-vous pour accepter.
    </p>

    @auth
        @if(auth()->user()->email === $invitation->email)
            <div class="flex space-x-4 justify-center">
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium">
                        Accepter l'invitation
                    </button>
                </form>
                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-medium">
                        Refuser
                    </button>
                </form>
            </div>
        @else
            <div class="bg-yellow-900/30 border border-yellow-700 text-yellow-300 px-4 py-3 rounded text-center">
                Cette invitation est destinée à <strong>{{ $invitation->email }}</strong>. Connectez-vous avec ce compte.
            </div>
        @endif
    @else
        <div class="flex space-x-4 justify-center">
            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium">
                Se connecter
            </a>
            <a href="{{ route('register') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 font-medium">
                S'inscrire
            </a>
        </div>
    @endauth
</x-guest-layout>
