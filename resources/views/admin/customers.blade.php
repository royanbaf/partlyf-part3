<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card { 
            background: rgba(30, 41, 59, 0.4); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden font-sans select-none">

    @include('layouts.admin-sidebar')

    <main class="flex-1 overflow-y-auto p-10 relative z-10">
        <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/10 rounded-full filter blur-[120px] pointer-events-none z-0"></div>

        <div class="mb-10 relative z-10">
            <h2 class="text-3xl font-black text-white tracking-tight">MANAJEMEN <span class="text-indigo-400">PELANGGAN</span></h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Kelola tingkatan pelanggan dan pantau aktivitas kemitraan Partlyfe.</p>
        </div>
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 font-bold text-sm flex items-center gap-2 relative z-10 shadow-[0_0_15px_rgba(16,185,129,0.05)] animate-fade-in">
                <i class="fa-solid fa-circle-check text-base"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
            @forelse($customers as $c)
                @php
                    $isB2b = $c->role === 'b2b';
                    $initials = strtoupper(substr($c->name, 0, 2));
                    
                    // Skema Warna Dinamis Berdasarkan Role Pengguna
                    $gradient = $isB2b ? 'from-purple-500 to-indigo-600' : 'from-amber-400 to-orange-500';
                    $badgeBg = $isB2b ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                    $shadow = $isB2b ? 'shadow-purple-500/20' : 'shadow-amber-500/20';
                @endphp

                <div class="glass-card p-6 rounded-3xl flex flex-col justify-between hover:border-white/10 hover:bg-slate-900/60 transition-all duration-300 relative group overflow-hidden shadow-xl">
                    
                    <div class="flex items-start gap-5 mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br {{ $gradient }} rounded-2xl flex items-center justify-center font-black text-slate-900 text-xl shadow-lg {{ $shadow }} flex-shrink-0 transition-transform group-hover:scale-105 duration-300">
                            {{ $initials }}
                        </div>
                        
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-white text-lg truncate group-hover:text-indigo-400 transition-colors">{{ $c->name }}</p>
                            <p class="text-xs text-slate-500 font-medium italic truncate mt-0.5">{{ $c->email }}</p>
                            
                            <div class="mt-2.5">
                                <span class="text-[9px] {{ $badgeBg }} px-2.5 py-1 rounded-lg border font-black uppercase tracking-wider inline-flex items-center gap-1.5">
                                    <i class="fa-solid {{ $isB2b ? 'fa-building-shield' : 'fa-user' }}"></i> 
                                    {{ $isB2b ? 'Mitra Bisnis B2B' : 'Retail B2C' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2.5 border-t border-white/5 pt-4 mt-2">
                        <a href="{{ route('admin.customers.show', $c->id) }}" class="flex-1 py-2.5 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl text-xs transition-all text-center flex items-center justify-center gap-1.5 border border-white/5 shadow-inner">
                            <i class="fa-solid fa-receipt opacity-70"></i> Riwayat Nota
                        </a>

                        @if(!$isB2b)
                            <form action="{{ route('admin.customers.upgrade', $c->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menaikkan skala akun {{ $c->name }} menjadi Mitra B2B?')">
                                @csrf
                                <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black rounded-xl text-xs transition-all shadow-md shadow-purple-600/10 flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrow-up-right-dots"></i> Jadi B2B
                                </button>
                            </form>
                        @else
                            <div class="flex-1 py-2.5 bg-purple-500/5 text-purple-400 font-bold rounded-xl text-[10px] uppercase tracking-wider text-center flex items-center justify-center gap-1 border border-purple-500/10 cursor-default">
                                <i class="fa-solid fa-circle-check text-purple-500"></i> Diskon Aktif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 glass-card border border-dashed border-white/10 rounded-3xl">
                    <i class="fa-solid fa-users-slash text-5xl text-slate-700 mb-4 block animate-pulse"></i>
                    <p class="text-slate-400 font-bold text-lg">Belum Ada Data Pelanggan</p>
                    <p class="text-xs text-slate-600 mt-1">Data pembeli retail maupun grosir masih kosong di database.</p>
                </div>
            @endforelse
        </div>
    </main>

</body>
</html>