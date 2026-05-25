<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kabar Admin | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(226, 232, 240, 0.8); 
        }
        .luxury-card-flat {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(148, 163, 184, 0.04);
        }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex">

    {{-- Ambil Sidebar Kiri Bawaan Kamu --}}
    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

        {{-- Header Atas --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50">
            <div class="text-sm font-bold text-slate-400">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-amber-600 transition-colors">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-40"></i>
                <span class="text-slate-700">Kabar Admin</span>
            </div>
            
            <div class="flex items-center gap-5">
                {{-- Ikon Lonceng (Di halaman ini otomatis tidak ada bintik merah karena semua sudah otomatis terbaca) --}}
                <a href="{{ route('customer.broadcast') }}" class="relative p-2.5 text-slate-700 bg-slate-100/80 transition-colors rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-bell text-lg"></i>
                </a>

                {{-- Avatar Profil --}}
                <a href="{{ route('customer.profile') }}" class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-500 text-slate-900 rounded-full flex items-center justify-center font-black text-sm transition-transform hover:scale-105" style="box-shadow: 0 4px 12px rgba(245,158,11,0.2);">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </a>
            </div>
        </header>

        {{-- Konten Utama List Broadcast --}}
        <main class="flex-1 overflow-y-auto p-8 bg-[#f8fafc]">
            <div class="max-w-[800px] mx-auto">
                <h1 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-tower-broadcast text-indigo-500"></i> Pusat Informasi & Pengumuman
                </h1>

                @if(isset($broadcasts) && $broadcasts->count() > 0)
                    <div class="space-y-6">
                        @foreach($broadcasts as $info)
                        <div class="luxury-card-flat rounded-3xl p-6 bg-white border border-slate-200/60 transition-all hover:shadow-md">
                            {{-- Info Pengirim & Tanggal --}}
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 text-xs">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-800 uppercase tracking-wider">Official Admin Partlyfe</p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $info->created_at->format('d M Y - H:i') }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full font-black text-[9px] uppercase tracking-wider border border-emerald-100">
                                    Pesan Baru
                                </span>
                            </div>

                            {{-- Judul & Isi Pesan Kabar Admin --}}
                            <h3 class="text-lg font-black text-slate-900 leading-snug mb-3">{{ $info->title }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed whitespace-pre-line">{{ $info->message }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    {{-- Tampilan Kosong --}}
                    <div class="py-20 text-center rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <i class="fa-solid fa-bell-slash text-5xl text-slate-200 mb-4"></i>
                        <p class="text-base text-slate-500 font-bold">Belum ada kabar berita</p>
                        <p class="text-xs text-slate-400 mt-1">Pengumuman penting, diskon suku cadang, atau jadwal maintenance sistem akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

</body>
</html>