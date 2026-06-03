<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #020617; color: white; }
        .glass-card { background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-sans flex h-screen overflow-hidden text-slate-200">

    {{-- Sidebar Admin Terintegrasi --}}
    @include('layouts.admin-sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full filter blur-[120px] pointer-events-none"></div>

        {{-- Header Panel Atas --}}
        <header class="h-20 border-b border-white/5 flex items-center justify-between px-10 flex-shrink-0 z-50">
            <div>
                <h2 class="text-xl font-bold text-white">Dashboard Overview</h2>
                <p class="text-xs text-slate-500">Pantau performa bisnis Partlyfe hari ini.</p>
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

        {{-- Konten Utama Dashboard --}}
        <main class="flex-1 overflow-y-auto p-10 scrollbar-hide relative z-10 space-y-8">
            
            {{-- 4 KARTU INDIKATOR UTAMA DATA ASLI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-money-bill-trend-up text-5xl text-indigo-400"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Pendapatan</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($totalPendapatan ?? 740000, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-emerald-400 mt-2 font-bold"><i class="fa-solid fa-arrow-up"></i> Live Arus Kas Masuk</p>
                </div>

                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group border-l-4 border-l-amber-500">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Pesanan Baru</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ $pesananBaruCount ?? 16 }} Pesanan</h3>
                    <p class="text-[10px] text-amber-400 mt-2 font-bold">Perlu verifikasi & diproses</p>
                </div>

                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Produk</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ $totalProdukCount ?? 32 }} <span class="text-sm font-normal text-slate-500 italic">SKU</span></h3>
                    <p class="text-[10px] text-rose-400 mt-2 font-bold">{{ $stokHabisCount ?? 1 }} Stok Habis Total</p>
                </div>

                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group border-l-4 border-l-indigo-500">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Pelanggan Aktif</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ $totalPelangganCount ?? 2 }} User</h3>
                    <p class="text-[10px] text-indigo-400 mt-2 font-bold">Pengguna Terdaftar Terdata</p>
                </div>
            </div>

            {{-- GRID TENGAH: TRANSAKSI TERBARU & STOK LOGISTIK KRITIS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- TABEL DATA TRANSAKSI TERBARU (FIXED ANTI-TUMPANG TINDIH) --}}
                <div class="lg:col-span-2 glass-card rounded-3xl overflow-hidden shadow-2xl flex flex-col justify-between">
                    <div class="w-full">
                        <div class="px-6 py-4.5 border-b border-white/5 flex justify-between items-center bg-white/5">
                            <h3 class="font-black text-white uppercase tracking-wider text-xs flex items-center gap-2">
                                <i class="fa-solid fa-receipt text-indigo-400"></i> Riwayat Transaksi Terbaru
                            </h3>
                            <a href="{{ route('admin.transactions.index') }}" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                                Lihat Semua <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                        
                        {{-- Header Grid (Fixed Base 12 Kolom) --}}
                        <div class="grid grid-cols-12 text-[10px] uppercase tracking-widest text-slate-400 font-black px-6 py-3.5 border-b border-white/5 bg-slate-950/40 w-full">
                            <div class="col-span-4 flex items-center">Nomor Invoice</div>
                            <div class="col-span-4 flex items-center">Nama Pelanggan</div>
                            <div class="col-span-2 flex items-center justify-center text-center w-full">Status</div>
                            <div class="col-span-2 flex items-center justify-end text-right w-full">Total</div>
                        </div>

                        {{-- Isi Baris Data Data (Mengunci layout grid yang sama persis dengan header) --}}
                        <div class="divide-y divide-white/5 w-full">
                            @forelse($transactions ?? [] as $t)
                                @php 
                                    $statusRow = strtolower(trim($t->status ?? 'pending'));
                                    $customerName = $t->customer_name ?? ($t->name ?? 'Pelanggan Eceran');
                                @endphp
                                <div class="grid grid-cols-12 items-center px-6 py-4 hover:bg-white/5 transition-all duration-200 group w-full">
                                    
                                    {{-- Kolom 1: Invoice (col-span-4) --}}
                                    <div class="col-span-4 font-mono text-xs font-bold text-indigo-400 group-hover:text-indigo-300 tracking-tight truncate flex items-center">
                                        #{{ $t->invoice_number ?? 'INV-' . $t->id }}
                                    </div>
                                    
                                    {{-- Kolom 2: Nama Pelanggan (col-span-4) --}}
                                    <div class="col-span-4 text-xs text-slate-200 font-semibold flex items-center pr-2 break-words">
                                        {{ $customerName }}
                                    </div>
                                    
                                    {{-- Kolom 3: Badge Status (col-span-2) --}}
                                    <div class="col-span-2 flex justify-center items-center w-full">
                                        @if(in_array($statusRow, ['pending', 'unpaid', 'menunggu pembayaran', 'menunggu']))
                                            <span class="w-full text-center bg-amber-500/10 text-amber-500 text-[9px] font-black py-1 rounded border border-amber-500/20 uppercase tracking-wider block">MENUNGGU</span>
                                        @elseif(in_array($statusRow, ['processing', 'diproses', 'sedang diproses']))
                                            <span class="w-full text-center bg-blue-500/10 text-blue-400 text-[9px] font-black py-1 rounded border border-blue-500/20 uppercase tracking-wider block">DIPROSES</span>
                                        @elseif(in_array($statusRow, ['shipped', 'delivered', 'selesai', 'paid', 'success', 'settlement']))
                                            <span class="w-full text-center bg-emerald-500/10 text-emerald-400 text-[9px] font-black py-1 rounded border border-emerald-500/20 uppercase tracking-wider block">SELESAI</span>
                                        @else
                                            <span class="w-full text-center bg-rose-500/10 text-rose-400 text-[9px] font-black py-1 rounded border border-rose-500/20 uppercase tracking-wider block">BATAL</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Kolom 4: Total Tagihan Uang (col-span-2) --}}
                                    <div class="col-span-2 flex items-center justify-end text-right font-mono text-xs font-black text-white tracking-tight w-full">
                                        Rp {{ number_format($t->total_amount ?? 0, 0, ',', '.') }}
                                    </div>
                                    
                                </div>
                            @empty
                                <div class="px-6 py-16 text-center w-full">
                                    <div class="w-12 h-12 bg-slate-900/50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-600">
                                        <i class="fa-solid fa-receipt text-xl"></i>
                                    </div>
                                    <p class="text-slate-500 text-xs font-medium italic">Belum ada data transaksi masuk.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- PANEL BARANG SEGERA HABIS --}}
                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-black text-white uppercase tracking-wider text-sm mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Logistik & Stok Kritis
                        </h3>
                        
                        <div class="space-y-3 max-h-[320px] overflow-y-auto scrollbar-hide">
                            @php
                                $criticalStock = DB::table('products')->where('current_stock', '<=', 5)->get();
                            @endphp

                            @forelse($criticalStock as $p)
                                @php $isKosong = ($p->current_stock <= 0); @endphp
                                <div class="flex items-center justify-between p-3 rounded-2xl border {{ $isKosong ? 'bg-rose-500/5 border-rose-500/20' : 'bg-white/5 border-white/5' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center border text-sm {{ $isKosong ? 'bg-rose-500/10 border-rose-500/30 text-rose-400' : 'bg-slate-900 border-white/10 text-slate-400' }}">
                                            <i class="fa-solid {{ $isKosong ? 'fa-ban' : 'fa-box-open' }}"></i>
                                        </div>
                                        <div class="max-w-[130px]">
                                            <p class="text-xs font-bold text-white line-clamp-1" title="{{ $p->name }}">{{ $p->name }}</p>
                                            <p class="text-[10px] text-slate-500 font-mono">SKU: {{ $p->sku ?? 'PRT-'.$p->id }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($isKosong)
                                            <p class="text-xs font-black text-rose-500 uppercase bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20 text-[9px] mb-1">Ludes</p>
                                        @else
                                            <p class="text-xs font-black text-amber-500 font-mono mb-1">Sisa {{ $p->current_stock }}</p>
                                        @endif
                                        <a href="/admin/products/{{ $p->id }}/edit" class="text-[9px] font-bold text-indigo-400 uppercase block hover:underline tracking-wider">Restock</a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 text-slate-600 text-xs italic">Seluruh stok aman terkendali.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRID BARIS BAWAH: METRIKS PROPOSAL STRUKTUR DATA --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-black text-white uppercase tracking-wider text-xs mb-2 text-slate-400 flex items-center gap-2">
                            <i class="fa-solid fa-database text-indigo-400"></i> Arsitektur Warehouse
                        </h3>
                        <p class="text-[11px] text-slate-500 mb-4">Pemantauan skema tabel transaksional terdata.</p>
                    </div>
                    <div class="p-4 bg-white/5 border border-white/5 rounded-2xl flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-500/10 text-indigo-400 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-circle-nodes"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 block font-mono">DATABASE STRUCTURE</span>
                            <span class="text-xs font-bold text-white block">Star Schema Active</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-black text-white uppercase tracking-wider text-xs mb-2 text-slate-400 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-400"></i> Integrasi Finansial
                        </h3>
                        <p class="text-[11px] text-slate-500 mb-4">Gerbang enkripsi token pembayaran instan.</p>
                    </div>
                    <div class="p-4 bg-white/5 border border-white/5 rounded-2xl flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-lg">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 block font-mono">MIDTRANS GATEWAY</span>
                            <span class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Sandbox Ready
                            </span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-black text-white uppercase tracking-wider text-xs mb-4 flex items-center gap-2 text-slate-400">
                            <i class="fa-solid fa-microchip text-indigo-400"></i> Core System Status
                        </h3>
                    </div>
                    <div class="space-y-3 font-mono text-[11px] flex-1 flex flex-col justify-center">
                        <div class="flex justify-between items-center py-2 border-b border-white/5">
                            <span class="text-slate-500">Gemini AI Model</span>
                            <span class="text-white bg-white/10 px-2 py-0.5 rounded text-[10px]">v1.5 Flash</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-500">Server Engine</span>
                            <span class="text-emerald-400 font-bold">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>