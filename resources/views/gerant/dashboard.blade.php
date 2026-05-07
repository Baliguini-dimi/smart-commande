<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gérant — Smart Commande</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="text-5xl mb-4">📊</div>
        <h1 class="text-2xl font-bold text-blue-700 mb-2">
            Bienvenue, {{ auth()->user()->name }} !
        </h1>
        <p class="text-gray-500 mb-6">Dashboard Gérant — En construction</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700">
                Se déconnecter
            </button>
        </form>
    </div>
</body>
</html>