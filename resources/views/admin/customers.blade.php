<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pelanggan | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden">
    @include('layouts.admin-sidebar')
    <main class="flex-1 overflow-y-auto p-10">
        <h2 class="text-2xl font-black text-white mb-10">Manajemen Pelanggan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-slate-900/40 p-6 rounded-3xl border border-white/5 flex items-center gap-5 hover:border-indigo-500/30 transition">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center font-black text-white text-xl shadow-lg">
                    AS
                </div>
                <div>
                    <p class="font-bold text-white text-lg">Agus Surya</p>
                    <p class="text-xs text-slate-500 font-medium italic">agus@example.com</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-[10px] bg-indigo-500/10 text-indigo-400 px-2 py-0.5 rounded font-bold uppercase">Member B2C</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>