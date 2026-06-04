<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .text-gradient-animated {
            background: linear-gradient(270deg, #d97706, #db2777, #d97706, #fb7185);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease infinite;
        }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-b: 1px solid rgba(226, 232, 240, 0.8); 
        }
        .luxury-card-flat {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(148, 163, 184, 0.05);
        }
        .rec-card-light {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .rec-card-light:hover {
            border-color: rgba(245,158,11,0.4);
            transform: translateY(-4px);
            box-shadow: 0 16px 25px -10px rgba(245,158,11,0.1);
        }

        .qty-btn-light {
            width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700; transition: all 0.15s; cursor: pointer;
        }
        .qty-btn-light:hover { background: rgba(0,0,0,0.05); }
        .qty-btn-light:active { transform: scale(0.9); }

        .particle { position: absolute; width: 4px; height: 4px; border-radius: 50%; pointer-events: none; animation: particleFloat var(--dur) ease-in var(--delay) infinite; }
        @keyframes particleFloat {
            0%   { transform: translateY(0) rotate(0deg); opacity: 0; }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.3; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        main::-webkit-scrollbar { width: 5px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 3px; }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex selection:bg-amber-100 selection:text-amber-900">

    @include('layouts.sidebar')

    {{-- KUNCI UTAMA: Inisialisasi Harga Retail ditaruh paling atas agar aman --}}
    @php 
        $retailPrice = $product->prices->where('price_level', 1)->first(); 
        $displayPrice = $retailPrice->price ?? 0;
        // Diskon khusus B2B: potongan harga Rp 5.000
        $isB2b = Auth::check() && strtolower(Auth::user()->role) === 'b2b';
        if ($isB2b) {
            $displayPrice = max(0, $displayPrice - 5000);
        }
    @endphp

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

        {{-- Header --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            <form action="{{ route('customer.dashboard') }}" method="GET" class="relative w-full max-w-3xl flex-grow">
                <input type="text" name="search" placeholder="Cari sparepart lain di Partlyfe..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-12 pr-6 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all text-sm text-slate-800 placeholder-slate-400 outline-none">
                <button type="submit" class="absolute left-4 top-2.5 text-slate-400 hover:text-amber-600 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
            <div class="flex items-center gap-6 ml-8">
                <a href="{{ Auth::check() ? route('customer.wishlist') : route('login') }}" class="relative text-slate-400 hover:text-rose-500 transition-all hover:scale-110">
                    <i class="fa-solid fa-heart text-xl"></i>
                </a>
                <a href="{{ Auth::check() ? route('customer.cart') : route('login') }}" class="relative text-slate-400 hover:text-amber-500 transition-all hover:scale-110">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                </a>
                <div class="h-6 w-px bg-slate-200"></div>
                @auth
                <a href="{{ route('customer.profile') }}" class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-500 text-slate-900 rounded-full flex items-center justify-center font-black text-sm transition-transform hover:scale-110" style="box-shadow: 0 4px 12px rgba(245,158,11,0.3);">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </a>
                @else
                <a href="{{ route('login') }}" class="text-xs font-bold text-amber-700 bg-amber-500/10 px-4 py-2 rounded-full border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all">Masuk</a>
                @endauth
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 relative" id="main-scroll">
            <div class="max-w-[1200px] mx-auto">

                {{-- Breadcrumb --}}
                <nav class="text-xs font-medium text-slate-400 mb-8 flex items-center gap-2">
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-amber-600 transition-colors">Beranda</a>
                    <i class="fa-solid Mount chevron-right text-[8px] opacity-40"></i>
                    <span class="text-slate-600 font-semibold">{{ $product->name }}</span>
                </nav>

                <div class="flex gap-8 items-start">

                    {{-- LEFT — PRODUCT IMAGE --}}
                    <div class="w-[340px] flex-shrink-0 sticky top-4">
                        <div class="luxury-card-flat rounded-3xl aspect-square relative overflow-hidden p-4 flex items-center justify-center bg-white">
                            @if($product->images && $product->images->isNotEmpty())
                                <img src="{{ asset('storage/products/' . basename(optional($product->images->first())->image_path ?? 'default.png')) }}"
                                    alt="{{ $product->name }}"
                                    onerror="this.onerror=null; this.src='https://placehold.co/400x400/f8fafc/b45309?text=Foto+Menyusul';"
                                    class="max-w-full max-h-full object-contain transition-transform duration-500 hover:scale-102">
                            @else
                                <i class="fa-solid fa-box-open text-6xl text-slate-200"></i>
                            @endif

                            <div class="absolute top-4 left-4 z-10">
                                <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded bg-amber-500/10 border border-amber-500/20 text-amber-800">100% Original</span>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            @foreach([
                                ['fa-shield-halved', "Garansi\nResmi"],
                                ['fa-truck-fast', "Kirim\nCepat"],
                                ['fa-rotate-left', "Retur\nMudah"],
                            ] as $badge)
                            <div class="luxury-card-flat rounded-2xl p-3 flex flex-col items-center gap-1.5 text-center bg-white">
                                <i class="fa-solid {{ $badge[0] }} text-amber-600 text-sm"></i>
                                <span class="text-[9px] text-slate-500 leading-tight font-bold whitespace-pre">{{ $badge[1] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- MIDDLE — PRODUCT INFO --}}
                    <div class="flex-grow min-w-0">
                        <div class="mb-2">
                            <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded bg-indigo-5 border border-indigo-100 text-indigo-700">
                                {{ $product->category->name ?? 'Spare Part' }}
                            </span>
                        </div>

                        <h1 class="text-3xl font-black leading-tight mb-2 text-slate-800">
                            {{ $product->name }}
                        </h1>

                        <div class="flex items-center gap-4 mb-6 text-sm">
                            <div class="flex items-center gap-1 text-amber-500 font-bold">
                                <i class="fa-solid fa-star"></i> 4.9
                            </div>
                            <span class="text-slate-300">•</span>
                            <p class="text-slate-500">Terjual <span class="font-bold text-slate-700">1.2 rb+</span></p>
                            <span class="text-slate-300">•</span>
                            <div class="text-emerald-600 text-xs font-bold flex items-center gap-1">
                                <i class="fa-solid fa-circle text-[6px]"></i> Ready Stock
                            </div>
                        </div>

                        <div class="mb-8">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Harga Retail</p>
                            <h2 class="text-4xl font-black text-gradient-animated leading-none">
                                @if($isB2b)
                                    <span class="text-[10px] text-emerald-500 font-bold">DISKON B2B Rp 5.000</span><br>
                                @endif
                                Rp {{ number_format($displayPrice, 0, ',', '.') }}
                            </h2>
                        </div>

                        <div class="luxury-card-flat rounded-2xl p-6 mb-5 bg-white">
                            <h3 class="font-black text-slate-800 mb-4 text-base flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-amber-500"></i> Detail Suku Cadang
                            </h3>
                            <div class="space-y-0 text-sm divide-y divide-slate-100">
                                @foreach([
                                    ['Merk', $product->brand, 'text-amber-700 font-bold'],
                                    ['Kategori', $product->category->name ?? 'Uncategorized', 'text-slate-700 font-semibold'],
                                    ['Kondisi', 'Baru / Segel Pabrik', 'text-emerald-600 font-bold'],
                                ] as $row)
                                <div class="flex items-center py-3">
                                    <p class="w-36 text-slate-400 text-xs font-bold uppercase tracking-wider">{{ $row[0] }}</p>
                                    <p class="{{ $row[2] }}">{{ $row[1] }}</p>
                                </div>
                                @endforeach
                                <div class="flex items-center py-3">
                                    <p class="w-36 text-slate-400 text-xs font-bold uppercase tracking-wider">SKU Part</p>
                                    <code class="text-amber-800 text-xs bg-amber-500/5 border border-amber-500/20 px-2.5 py-1 rounded-lg font-mono font-bold">
                                        {{ $product->item_code }}
                                    </code>
                                </div>
                            </div>
                        </div>

                        <div class="luxury-card-flat rounded-2xl p-6 bg-white">
                            <h3 class="font-black text-slate-800 mb-3 text-base flex items-center gap-2">
                                <i class="fa-solid fa-file-lines text-indigo-500"></i> Informasi Deskripsi
                            </h3>
                            <p class="text-slate-500 leading-relaxed text-sm">
                                Suku cadang original berkualifikasi tinggi dari merek <span class="text-amber-600 font-bold">{{ $product->brand }}</span>, diproduksi dengan presisi tinggi menggunakan standar industri otomotif global demi menjaga keandalan berkendara harian Anda.
                            </p>
                        </div>
                    </div>

                    {{-- RIGHT — BUY PANEL LIGHT --}}
                    <div class="w-[300px] flex-shrink-0 sticky top-4">
                        <div class="luxury-card-flat rounded-3xl p-6 bg-white relative overflow-hidden shadow-sm">
                            <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none" id="particles-container"></div>

                            @php $isOutofStock = $product->current_stock <= 0; @endphp

                            @if($isOutofStock)
                                <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-700 text-xs p-3.5 rounded-xl">
                                    Stok sedang kosong. Simpan ke Wishlist untuk memantau restock otomatis.
                                </div>
                                <button disabled class="w-full py-3.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">
                                    Tidak Bisa Dibeli
                                </button>
                            @else
                                @auth
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-5">
                                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-3">Atur Kuantitas</p>
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl p-1">
                                                <button type="button" onclick="decreaseQty()" class="qty-btn-light text-slate-500">−</button>
                                                <span id="qtyDisplay" class="w-10 text-center font-black text-slate-800 text-base select-none">1</span>
                                                <input type="hidden" id="qtyInput" name="qty" value="1">
                                                <button type="button" onclick="increaseQty({{ $product->current_stock }})" class="qty-btn-light text-amber-600">+</button>
                                            </div>
                                            <p class="text-xs text-slate-400">Sisa: <span class="text-slate-700 font-bold">{{ $product->current_stock }} unit</span></p>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center mb-5 p-3.5 rounded-xl bg-amber-5/40 border border-amber-100">
                                        <p class="text-xs text-slate-500 font-bold">Total Pembayaran</p>
                                        <p class="font-black text-amber-700 text-lg" id="subtotalText">
                                            Rp {{ number_format($retailPrice->price ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2.5">
                                        <button type="submit" class="w-full bg-gradient-to-r from-amber-400 to-amber-500 text-slate-900 font-black py-3.5 rounded-xl text-sm hover:brightness-105 transition-all shadow-md shadow-amber-500/10 flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                                        </button>
                                        <button type="button" id="btn-beli-langsung" data-product-id="{{ $product->id }}" class="w-full bg-white border border-amber-500 text-amber-600 font-black py-3.5 rounded-xl text-sm hover:bg-amber-50/5 transition-all flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-bolt"></i> Beli Langsung
                                        </button>
                                    </div>
                                </form>
                                @else
                                <div class="space-y-2.5">
                                    <a href="{{ route('login') }}" class="w-full bg-amber-500 text-slate-900 font-black py-3.5 rounded-xl text-sm flex items-center justify-center gap-2 shadow-md">Masukkan Keranjang</a>
                                    <a href="{{ route('login') }}" class="w-full bg-white border border-amber-500 text-amber-600 font-black py-3.5 rounded-xl text-sm flex items-center justify-center">Beli Langsung</a>
                                </div>
                                @endauth
                            @endif

                            {{-- Wishlist Toggle --}}
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                @auth
                                @php $inWishlist = \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists(); @endphp
                                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 text-xs font-bold py-2 rounded-xl hover:bg-slate-50 transition-all {{ $inWishlist ? 'text-rose-500' : 'text-slate-400 hover:text-rose-500' }}">
                                        <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                        {{ $inWishlist ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-slate-400 hover:text-rose-500 transition-all py-2 rounded-xl hover:bg-slate-50">
                                    <i class="fa-regular fa-heart"></i> Simpan ke Wishlist
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                </div>

                {{-- AI RECOMMENDATIONS --}}
                @if(isset($recommendations) && $recommendations->count() > 0)
                <div class="mt-20">
                    <div class="h-px bg-slate-200 mb-10"></div>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-lg">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800">AI Smart Recommendations</h2>
                            <p class="text-xs text-slate-400">Komponen rekomendasi terintegrasi AI luar berbasis kompatibilitas mekanis</p>
                        </div>
                    </div>

                    <div class="flex gap-4 overflow-x-auto pb-6 no-scrollbar snap-x">
                        @foreach($recommendations as $rec)
@php
                            $recPrice = $rec->prices->where('price_level', 1)->first();
                            $recIsOutofStock = $rec->current_stock <= 0;
                            $recDisplayPrice = $recPrice->price ?? 0;
                            if (Auth::check() && Auth::user()->role === 'B2B') {
                                $recDisplayPrice = max(0, $recDisplayPrice - 5000);
                            }
                        @endphp

                        <div class="snap-start flex-shrink-0 w-[200px] rec-card-light rounded-2xl flex flex-col relative overflow-hidden bg-white {{ $recIsOutofStock ? 'opacity-50' : '' }}">
                            <a href="{{ route('product.detail', $rec->id) }}" class="flex flex-col h-full">
                                <div class="h-36 bg-white flex items-center justify-center relative overflow-hidden border-b border-slate-100 p-2">
                                    @if($rec->current_stock > 0 && isset($rec->cashback_percent) && $rec->cashback_percent > 0)
                                    <div class="absolute top-2 left-2 bg-rose-500 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-sm z-10">
                                        PROMO {{ $rec->cashback_percent }}%
                                    </div>
                                    @endif

                                    @if($rec->images && $rec->images->isNotEmpty())
                                        <img src="{{ asset('storage/products/' . basename(optional($rec->images->first())->image_path ?? 'default.png')) }}"
                                            alt="{{ $rec->name }}"
                                            onerror="this.onerror=null; this.src='https://placehold.co/200x200/f8fafc/b45309?text=No+Pic';"
                                            class="max-w-full max-h-full object-contain p-2 transition-transform duration-300 hover:scale-103">
                                    @else
                                        <i class="fa-solid fa-box-open text-3xl text-slate-200"></i>
                                    @endif
                                </div>
                                <div class="p-4 flex flex-col flex-grow">
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="text-[9px] text-amber-600 font-black uppercase tracking-wider">{{ $rec->brand }}</p>
                                        @php
                                            $matchPercentage = 98 - ($loop->index * rand(3, 5)); 
                                            if($matchPercentage < 65) $matchPercentage = rand(65, 71);
                                        @endphp
                                        <span class="text-[9px] bg-indigo-50 text-indigo-600 border border-indigo-100 px-1.5 py-0.5 rounded font-mono font-bold">
                                             {{ $matchPercentage }}% Match
                                        </span>
                                    </div>
                                    <h3 class="text-xs font-bold text-slate-700 leading-snug line-clamp-2 mb-3 h-8">{{ $rec->name }}</h3>
                                    <p class="font-black text-sm text-slate-900 mt-auto">Rp {{ number_format($recDisplayPrice, 0, ',', '.') }}</p>
                                </div>
                            </a>

                            @if(!$recIsOutofStock)
                            @auth
                            <div class="px-4 pb-4">
                                <form action="{{ route('cart.add', $rec->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all">
                                        <i class="fa-solid fa-cart-plus mr-1"></i> Keranjang
                                    </button>
                                </form>
                            </div>
                            @endauth
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="h-20"></div>
            </div>
        </main>
    </div>

    <script>
        const retailPrice = {{ $retailPrice->price ?? 0 }};
        const isB2b = {{ $isB2b ? 'true' : 'false' }};
        const discountB2b = 5000;
        const effectivePrice = isB2b ? Math.max(0, retailPrice - discountB2b) : retailPrice;
        const qtyInput    = document.getElementById('qtyInput');
        const qtyDisplay  = document.getElementById('qtyDisplay');
        const subtotalText = document.getElementById('subtotalText');

        function updateSubtotal() {
            const total = parseInt(qtyInput.value) * effectivePrice;
            subtotalText.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
        function increaseQty(max) {
            if (parseInt(qtyInput.value) < max) {
                const v = parseInt(qtyInput.value) + 1;
                qtyInput.value = v;
                qtyDisplay.innerText = v;
                updateSubtotal();
                spawnParticles();
            }
        }
        function decreaseQty() {
            if (parseInt(qtyInput.value) > 1) {
                const v = parseInt(qtyInput.value) - 1;
                qtyInput.value = v;
                qtyDisplay.innerText = v;
                updateSubtotal();
            }
        }

        function spawnParticles() {
            const container = document.getElementById('particles-container');
            if (!container) return;
            for (let i = 0; i < 6; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.cssText = `
                    left: ${20 + Math.random() * 60}%;
                    bottom: 30%;
                    --dur: ${1.2 + Math.random()}s;
                    --delay: ${Math.random() * 0.3}s;
                    background: ${Math.random() > 0.5 ? '#f59e0b' : '#fb7185'};
                `;
                container.appendChild(p);
                setTimeout(() => p.remove(), 2000);
            }
        }

        const btnBeliLangsung = document.getElementById('btn-beli-langsung');
        if (btnBeliLangsung) {
            btnBeliLangsung.addEventListener('click', function() {
                const productId   = this.getAttribute('data-product-id');
                const qty         = document.getElementById('qtyInput').value;
                
                this.innerHTML  = '<i class="fa-solid fa-spinner fa-spin"></i> Memuat Ringkasan...';
                this.disabled   = true;

                // Lempar ke halaman Ringkasan Belanja dengan membawa ID & Qty
                window.location.href = `/customer/checkout?product_id=${productId}&qty=${qty}`;
            });
        }
    </script>
</body>
</html>