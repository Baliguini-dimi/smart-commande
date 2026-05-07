<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Smart Commande</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-950 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="text-5xl mb-4">👑</div>
        <h1 class="text-2xl font-bold text-white mb-2">
            Super Admin Panel
        </h1>
        <p class="text-gray-400 mb-6">Connecté en tant que {{ auth()->user()->name }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold">
                Se déconnecter
            </button>
        </form>
    </div>
</body>
</html>