<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyColoc - Gestion de Colocation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-900">
    <nav class="bg-gray-800 shadow-sm border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-400">EasyColoc</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-indigo-400 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-indigo-400 font-medium">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 font-medium">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-100 tracking-tight">
                Gérez votre <span class="text-indigo-400">colocation</span> simplement
            </h1>
            <p class="mt-6 text-xl text-gray-400 max-w-2xl mx-auto">
                Suivez les dépenses communes, calculez automatiquement qui doit quoi à qui, et simplifiez la vie en colocation.
            </p>
            <div class="mt-10 flex justify-center space-x-4">
                @guest
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-500 transition">
                        Commencer gratuitement
                    </a>
                    <a href="{{ route('login') }}" class="bg-gray-800 text-indigo-400 border-2 border-indigo-500 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-700 transition">
                        Se connecter
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-500 transition">
                        Aller au Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <div class="bg-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-100">Fonctionnalités</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-xl bg-gray-700/50 border border-gray-700">
                    <div class="w-12 h-12 bg-indigo-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-100 mb-2">Gestion des membres</h3>
                    <p class="text-gray-400">Invitez vos colocataires par email et gérez facilement votre colocation.</p>
                </div>
                <div class="text-center p-6 rounded-xl bg-gray-700/50 border border-gray-700">
                    <div class="w-12 h-12 bg-green-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-100 mb-2">Suivi des dépenses</h3>
                    <p class="text-gray-400">Ajoutez vos dépenses avec catégories et suivez l'historique complet.</p>
                </div>
                <div class="text-center p-6 rounded-xl bg-gray-700/50 border border-gray-700">
                    <div class="w-12 h-12 bg-purple-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-100 mb-2">Calcul automatique</h3>
                    <p class="text-gray-400">Les soldes et remboursements sont calculés automatiquement.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-950 text-gray-400 py-8 mt-16 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} EasyColoc. Projet de gestion de colocation.</p>
        </div>
    </footer>
</body>
</html>
