<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #020617; color: white; }
        .glass-card { background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="font-sans flex h-screen overflow-hidden text-slate-200">

    {{-- Sidebar Admin --}}
    @include('layouts.admin-sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full filter blur-[120px] pointer-events-none"></div>

        {{-- Header Atas Panel --}}
        <header class="h-20 border-b border-white/5 flex items-center justify-between px-10 flex-shrink-0 z-40">
            <div>
                <h2 class="text-xl font-bold text-white">Manajemen Pelanggan</h2>
                <p class="text-xs text-slate-500">Kelola tingkatan pelanggan dan pantau aktivitas kemitraan Partlyfe.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right mr-4 hidden md:block">
                    <p class="text-sm font-bold text-white">{{ Auth::user()->name ?? 'Admin Master' }}</p>
                    <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest">Administrator</p>
                </div>
                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center font-black text-slate-900 border-2 border-indigo-400/50">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Main Area Konten --}}
        <main class="flex-1 overflow-y-auto p-10 relative z-10">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                @forelse($customers ?? [] as $c)
                    <div class="glass-card rounded-3xl p-6 flex flex-col justify-between border border-white/5 relative overflow-hidden group">
                        
                        <div class="flex items-start gap-4 mb-6">
                            {{-- Avatar Lingkaran Inisial Nama --}}
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center font-black text-slate-900 text-lg shadow-md border border-amber-400/30 flex-shrink-0">
                                {{ strtoupper(substr($c->name ?? 'C', 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-base font-black text-white tracking-tight">{{ $c->name }}</h3>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $c->email }}</p>
                                <span class="inline-flex items-center gap-1 mt-3 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">
                                    <i class="fa-solid fa-user-tag text-[8px]"></i> Retail B2C
                                </span>
                            </div>
                        </div>

                        {{-- Tombol Aksi Bawah Kartu Pelanggan --}}
                        <div class="flex items-center gap-3 border-t border-white/5 pt-4 mt-auto">
                            {{-- 🚀 TOMBOL SAKTI: Mengarah ke fungsi detail nota pelanggan di controller --}}
                            <a href="{{ url('/admin/customers/' . $c->id) }}" 
                                class="flex-1 bg-white/5 border border-white/10 hover:bg-white/10 text-slate-300 hover:text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                                <i class="fa-solid fa-file-invoice opacity-50"></i> Riwayat Nota
                            </a>
                            
                            <button class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-indigo-600/10">
                                <i class="fa-solid fa-angles-up"></i> Jadi B2B
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center text-xs text-slate-500 italic glass-card rounded-3xl">
                        Belum ada data pelanggan terdaftar di sistem database Partlyfe.
                    </div>
                @endforelse
            </div>

        </main>
    </div>

</body>
</html>