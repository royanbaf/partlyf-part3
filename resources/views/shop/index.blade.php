<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PartLyfe | Premium Motorcycle Components</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background-color: #fafafa; 
            color: #121212; 
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        /* Aksen warna emas premium (Champagne / Cast Gold) */
        .text-gold { color: #c5a880; }
        .bg-gold { background-color: #c5a880; }
        .border-gold { border-color: #c5a880; }
        
        /* Card minimalis dengan transisi halus */
        .premium-card {
            background-color: #ffffff;
            border: 1px solid #eaeaea;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .premium-card:hover {
            border-color: #c5a880;
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(197, 168, 128, 0.05);
        }
    </style>
</head>
<body class="antialiased selection:bg-[#c5a880] selection:text-white">

    <header class="w-full bg-white/80 backdrop-blur sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="/" class="text-base font-bold tracking-[0.3em] text-gray-900 uppercase">
                PART<span class="text-gold">LYFE</span>
            </a>
            
            <nav class="hidden md:flex items-center gap-10 text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">
                <a href="#" class="text-gray-900 border-b border-gray-900 pb-1">Katalog Produk</a>
                <a href="#" class="hover:text-gray-900 transition">Manajemen Stok</a>
                <a href="#" class="hover:text-gray-900 transition">Rekomendasi Pintar</a>
                <a href="#" class="hover:text-gray-900 transition">Kemitraan B2B</a>
            </nav>

            <div class="flex items-center gap-6">
                <a href="{{ route('login') }}" class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500 hover:text-gray-900 transition">Sign In</a>
                <a href="{{ route('register') }}" class="text-[11px] font-semibold uppercase tracking-[0.2em] text-white bg-gray-900 hover:bg-[#c5a880] px-6 py-3 transition duration-300">
                    Register
                </a>
            </div>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-6 pt-20 pb-28 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
        <div class="lg:col-span-5 space-y-6">
            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gold block">
                Premium Component Registry
            </span>
            <h1 class="text-4xl sm:text-5xl font-light text-gray-900 tracking-tight leading-[1.15]">
                Suku Cadang Terbaik.<br>
                <span class="font-bold">Presisi Tanpa Batas.</span>
            </h1>
            <p class="text-gray-400 text-sm leading-relaxed max-w-sm font-light">
                Akses langsung ke pusat manajemen suku cadang motor original. Dirancang khusus untuk performa tinggi, ketahanan maksimal, dan transparansi harga distributor.
            </p>
            <div class="pt-4">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-4 text-[11px] font-semibold uppercase tracking-[0.2em] bg-gray-900 text-white hover:bg-[#c5a880] px-8 py-4 transition duration-300">
                    Buka Katalog <i class="fa-solid fa-arrow-right text-[10px] opacity-60"></i>
                </a>
            </div>
        </div>

        <div class="lg:col-span-7 grid grid-cols-12 gap-4 h-[400px] sm:h-[480px]">
            <div class="col-span-8 bg-white border border-gray-200 p-8 flex flex-col justify-between relative group cursor-pointer">
                <span class="text-[10px] text-gray-300 font-medium tracking-widest">COLLECTION 2026</span>
                <div class="w-full flex justify-center py-8 text-gray-200 group-hover:text-gold transition-colors duration-500">
                    <i class="fa-solid fa-gear text-7xl opacity-40"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-gold block mb-1">Komponen Mesin</span>
                    <h3 class="font-bold text-base text-gray-900 tracking-tight">Piston & Kopling Ganda Matic</h3>
                </div>
            </div>
            
            <div class="col-span-4 flex flex-col gap-4 h-full">
                <div class="h-1/2 bg-white border border-gray-200 p-6 flex flex-col justify-between hover:border-gold transition-colors">
                    <i class="fa-solid fa-circle-nodes text-2xl text-gray-300"></i>
                    <h4 class="font-bold text-xs text-gray-900 uppercase tracking-wider">Sistem CVT</h4>
                </div>
                <div class="h-1/2 bg-white border border-gray-200 p-6 flex flex-col justify-between hover:border-gold transition-colors">
                    <i class="fa-solid fa-droplet text-2xl text-gray-300"></i>
                    <h4 class="font-bold text-xs text-gray-900 uppercase tracking-wider">Cairan Fluida</h4>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between mb-16 gap-4">
            <div>
                <h2 class="text-xl font-bold uppercase tracking-wider text-gray-900">Kategori Pilihan</h2>
                <p class="text-xs text-gray-400 mt-1">Suku cadang yang dikelompokkan berdasarkan fungsionalitas sistem motor.</p>
            </div>
            <div class="flex gap-8 text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">
                <span class="text-gray-900 border-b border-gray-900 pb-2 cursor-pointer">Honda Series</span>
                <span class="hover:text-gray-900 cursor-pointer transition">Yamaha Series</span>
                <span class="hover:text-gray-900 cursor-pointer transition">Suzuki Series</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="space-y-4 cursor-pointer group">
                <div class="h-72 bg-white border border-gray-200 flex items-center justify-center text-gray-200 group-hover:border-gold transition duration-300 relative">
                    <i class="fa-solid fa-circle-notch text-4xl opacity-40 group-hover:text-gold transition-colors"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-widest text-gray-900">Drive System & CVT</h3>
                    <p class="text-xs text-gray-400 mt-0.5">V-Belt, Roller, Komponen Matik</p>
                </div>
            </div>
            <div class="space-y-4 cursor-pointer group">
                <div class="h-72 bg-white border border-gray-200 flex items-center justify-center text-gray-200 group-hover:border-gold transition duration-300 relative">
                    <i class="fa-solid fa-cubes text-4xl opacity-40 group-hover:text-gold transition-colors"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-widest text-gray-900">Komponen Mesin</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Piston Kit, Stang Seher, Blok Cylinder</p>
                </div>
            </div>
            <div class="space-y-4 cursor-pointer group">
                <div class="h-72 bg-white border border-gray-200 flex items-center justify-center text-gray-200 group-hover:border-gold transition duration-300 relative">
                    <i class="fa-solid fa-bolt-lightning text-4xl opacity-40 group-hover:text-gold transition-colors"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-widest text-gray-900">Kelistrikan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Aki, Busi Iridium, CDI Pengapian</p>
                </div>
            </div>
            <div class="space-y-4 cursor-pointer group">
                <div class="h-72 bg-white border border-gray-200 flex items-center justify-center text-gray-200 group-hover:border-gold transition duration-300 relative">
                    <i class="fa-solid fa-shield text-4xl opacity-40 group-hover:text-gold transition-colors"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-widest text-gray-900">Sistem Pengereman</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Cakram, Kaliper, Kampas Rem</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-20 border-t border-gray-200 mb-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-sm">
            <div class="space-y-3">
                <span class="text-gold font-bold text-xs tracking-widest block">01 / LOGISTICS</span>
                <h3 class="font-bold text-base text-gray-900 uppercase tracking-tight">Manajemen Stok Terencana</h3>
                <p class="text-gray-400 text-xs leading-relaxed font-light">
                    Sistem kami menganalisis data historis permintaan secara berkala guna memastikan pasokan komponen motor krusial selalu tersedia tepat waktu sebelum stok menipis.
                </p>
            </div>
            <div class="space-y-3">
                <span class="text-gold font-bold text-xs tracking-widest block">02 / ASSISTANCE</span>
                <h3 class="font-bold text-base text-gray-900 uppercase tracking-tight">Rekomendasi Pintar</h3>
                <p class="text-gray-400 text-xs leading-relaxed font-light">
                    Membantu Anda mengidentifikasi jenis dan kode suku cadang yang paling presisi sesuai dengan keluhan kendala teknis kendaraan Anda tanpa risiko salah beli.
                </p>
            </div>
            <div class="space-y-3">
                <span class="text-gold font-bold text-xs tracking-widest block">03 / DIRECT SOURCE</span>
                <h3 class="font-bold text-base text-gray-900 uppercase tracking-tight">Harga Tangan Pertama</h3>
                <p class="text-gray-400 text-xs leading-relaxed font-light">
                    Menghubungkan langsung ke pusat suplai utama untuk memastikan kestabilan harga komponen, transparansi produk, dan keaslian 100% suku cadang.
                </p>
            </div>
        </div>
    </section>

    <footer class="bg-[#121212] text-gray-400 py-16 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 text-xs border-b border-gray-800 pb-12">
            <div class="space-y-3">
                <span class="font-bold text-white tracking-[0.25em] uppercase">PART<span class="text-gold">LYFE</span></span>
                <p class="text-[11px] text-gray-500 leading-relaxed">Platform ekosistem distribusi suku cadang sepeda motor berstandar tinggi dan tepercaya.</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-bold text-white uppercase tracking-wider text-[10px] text-gold">Layanan</h4>
                <p class="hover:text-white cursor-pointer transition">Eceran Konsumen (B2C)</p>
                <p class="hover:text-white cursor-pointer transition">Grosir Bengkel (B2B)</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-bold text-white uppercase tracking-wider text-[10px] text-gold">Fitur Sistem</h4>
                <p class="hover:text-white cursor-pointer transition">Pemantau Tren Suplai</p>
                <p class="hover:text-white cursor-pointer transition">Asisten Identifikasi Part</p>
            </div>
            <div class="space-y-2">
                <h4 class="font-bold text-white uppercase tracking-wider text-[10px] text-gold">Informasi</h4>
                <p class="hover:text-white cursor-pointer transition">Hubungi Manajemen</p>
            </div>
        </div>
        <div class="max-w-6xl mx-auto pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] text-gray-600">
            <div>©️ 2026 PartLyfe Core System. Built by Lennard Lucius Huang. All rights reserved.</div>
            <div class="mt-2 sm:mt-0">Surabaya, Indonesia</div>
        </div>
    </footer>

</body>
</html>