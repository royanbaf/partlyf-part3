<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Suku Cadang Presisi | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        main::-webkit-scrollbar { width: 6px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-b: 1px solid rgba(226, 232, 240, 0.8); 
        }
        
        .luxury-card {
            background: #ffffff; border: 1px solid #e5e7eb; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .luxury-card:hover {
            border-color: rgba(245,158,11,0.3); transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(245,158,11,0.08);
        }

        .category-btn-active {
            background: #fffbeb !important; border-color: #f59e0b !important; color: #b45309 !important; font-weight: 700; box-shadow: 0 4px 12px rgba(245,158,11,0.08);
        }

        .customer-pagination-container nav > div:first-child { display: none; } 
        .customer-pagination-container nav > div:last-child { display: flex; justify-content: center; width: 100%; gap: 6px; }
        .customer-pagination-container .page-link, .customer-pagination-container span[aria-current="page"] > span, .customer-pagination-container nav span.relative {
            width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 12px !important; font-size: 13px; font-weight: 700; transition: all 0.2s ease;
        }
        .customer-pagination-container a.page-link, .customer-pagination-container span.page-link { background-color: #ffffff !important; color: #64748b !important; border: 1px solid #e2e8f0 !important; }
        .customer-pagination-container span[aria-current="page"] > span { background-color: #f59e0b !important; color: #ffffff !important; border: 1px solid #f59e0b !important; }

        /* 🤖 OVERLAY MEKANIK AI STYLING */
        #ai-chat-overlay {
            position: fixed; bottom: -600px; right: 30px; width: 380px; height: 500px;
            background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
            border-radius: 24px 24px 0 0; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); z-index: 2000;
        }
        #ai-chat-overlay.open { bottom: 0; }
        .floating-ai-btn {
            position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
            background: linear-gradient(135deg, #4f46e5, #6366f1); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; cursor: pointer; box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3); z-index: 1999;
            transition: all 0.3s;
        }
        .floating-ai-btn:hover { transform: scale(1.05); brightness: 110%; }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">

        {{-- Header / Search Bar --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            
            <div class="relative w-full max-w-3xl flex-grow z-[100]">
                <form id="ai-search-form" action="{{ route('customer.dashboard') }}" method="GET" class="relative">
                    <input type="text" name="search" id="ai-search-input" value="{{ $search ?? '' }}" 
                        placeholder="Ketik nama komponen presisi (Misal: 'Kampas Vario' atau 'Oli')..." autocomplete="off"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-12 pr-12 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all text-sm text-slate-800 placeholder-slate-400 outline-none shadow-sm">
                    
                    <button type="submit" class="absolute left-4 top-2.5 text-slate-400 hover:text-amber-600 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <div id="ai-loading" class="absolute right-4 top-3 hidden">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-500 text-lg"></i>
                    </div>
                </form>
            </div>
            
            <div class="flex items-center gap-5 ml-8 flex-shrink-0">
                {{-- 🔔 REKOMENDASI DOSEN: LONCENG NOTIFIKASI DI NAVBAR --}}
                @php
                    $unreadBroadcasts = Auth::check() ? \App\Models\Broadcast::where('user_id', Auth::id())->where('is_read', false)->count() : 0;
                @endphp
                <a href="{{ Auth::check() ? route('customer.broadcast') : route('login') }}" class="relative text-slate-400 hover:text-amber-500 transition-all flex items-center justify-center w-10 h-10 rounded-xl hover:bg-slate-50">
                    <i class="fa-solid fa-bell text-xl"></i>
                    @if($unreadBroadcasts > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                    @endif
                </a>

                <a href="{{ Auth::check() ? route('customer.wishlist') : route('login') }}" class="relative text-slate-400 hover:text-rose-500 transition-all flex items-center justify-center w-10 h-10 rounded-xl hover:bg-slate-50">
                    <i class="fa-solid fa-heart text-xl"></i>
                </a>
                <a href="{{ Auth::check() ? route('customer.cart') : route('login') }}" class="relative text-slate-400 hover:text-amber-500 transition-all flex items-center justify-center w-10 h-10 rounded-xl hover:bg-slate-50">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                </a>
                <div class="h-6 w-px bg-slate-200"></div>
                @auth
                <a href="{{ route('customer.profile') }}" class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-500 text-slate-900 rounded-full flex items-center justify-center font-black text-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </a>
                @else
                <a href="{{ route('login') }}" class="text-xs font-bold text-amber-700 bg-amber-500/10 px-4 py-2 rounded-full border border-amber-500/20">Masuk</a>
                @endauth
            </div>
        </header>

        {{-- Main Dashboard Content --}}
        <main class="flex-1 overflow-y-auto p-8 bg-[#f8fafc]">
            <div class="max-w-[1200px] mx-auto">

                {{-- BANNER PROFIL UTAMA --}}
                <div id="hero-profile-banner" class="mb-10 p-7 rounded-3xl bg-white border border-slate-200/80 relative overflow-hidden flex flex-wrap gap-6 justify-between items-center shadow-sm">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 text-slate-900 text-2xl font-black flex items-center justify-center shadow-sm">
                            {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Selamat Datang di Partlyfe,</p>
                            <h2 class="text-2xl font-black text-slate-900">{{ Auth::user()->name ?? 'Pelanggan Setia' }}</h2>
                            <p class="text-xs text-amber-700 mt-1 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-coins"></i> {{ Auth::user()->loyalty_points ?? 0 }} partlyfe points</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <a href="{{ route('customer.cart') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 flex flex-col items-center text-center w-28 hover:border-amber-200 transition-all group">
                            <i class="fa-solid fa-basket-shopping text-xl text-amber-500 mb-2.5"></i>
                            <p class="text-[11px] font-black text-slate-900 leading-tight">Keranjang</p>
                        </a>
                        <a href="{{ route('customer.transactions') }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 flex flex-col items-center text-center w-28 hover:border-amber-200 transition-all group">
                            <i class="fa-solid fa-receipt text-xl text-amber-500 mb-2.5"></i>
                            <p class="text-[11px] font-black text-slate-900 leading-tight">Riwayat Trx</p>
                        </a>
                        <button type="button" onclick="openAiOverlay()" class="p-4 rounded-2xl border border-indigo-100 bg-indigo-50/30 flex flex-col items-center text-center w-28 hover:border-indigo-300 transition-all group">
                            <i class="fa-solid fa-robot text-xl text-indigo-500 mb-2.5"></i>
                            <p class="text-[11px] font-black text-indigo-900 leading-tight">Mekanik AI</p>
                        </button>
                    </div>
                </div>

                {{-- MAIN SECTION LAYOUT --}}
                <div id="main-catalog-section">

                    {{-- Horizontal Categories Filter --}}
                    <div id="categories-filter-box" class="mb-8 p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-amber-500"></i> Jelajahi Kategori
                        </p>
                        <div class="flex gap-3 overflow-x-auto pb-1 no-scrollbar">
                            <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-500 transition-all {{ !$search ? 'category-btn-active' : '' }}">
                                Semua Suku Cadang
                            </a>
                            @foreach($categories as $cat)
                            <a href="{{ route('customer.dashboard', ['search' => $cat->name]) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-500 transition-all {{ isset($search) && strtolower($search) === strtolower($cat->name) ? 'category-btn-active' : '' }}">
                                {{ $cat->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <p id="catalog-title" class="text-xs text-slate-400 font-bold uppercase tracking-wider">
                            Daftar Komponen Presisi <span class="text-slate-600">(Total {{ $products->total() }} SKU)</span>
                        </p>
                    </div>

                    {{-- GRID CARD PRODUK UTAMA --}}
                    <div id="products-grid-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        @forelse($products as $prod)
                        @php 
                            $price = $prod->prices->where('price_level', 1)->first(); 
                            $isOutofStock = $prod->current_stock <= 0;
                        @endphp
                        
                        <div class="luxury-card rounded-2xl flex flex-col overflow-hidden relative {{ $isOutofStock ? 'opacity-80' : '' }}">
                            @if($isOutofStock)
                            <div class="absolute top-3 left-3 bg-rose-500 text-white text-[9px] font-black px-2.5 py-1 rounded shadow-sm z-20">STOK HABIS</div>
                            @elseif($prod->cashback_percent > 0)
                            <div class="absolute top-3 left-3 bg-rose-500 text-white text-[9px] font-black px-2 py-0.5 rounded z-20">Cashback {{ $prod->cashback_percent }}%</div>
                            @endif

                            <a href="{{ route('product.detail', $prod->id) }}" class="h-44 bg-white flex items-center justify-center p-4 border-b border-slate-100 relative overflow-hidden group-hover:scale-105 transition-transform duration-500">
                                @if($prod->images && $prod->images->isNotEmpty())
                                    <img src="{{ asset('storage/products/' . basename(optional($prod->images->first())->image_path ?? 'default.png')) }}" alt="{{ $prod->name }}"
                                        onerror="this.onerror=null; this.src='https://placehold.co/300x300/f8fafc/b45309?text=Foto+Menyusul';"
                                        class="max-w-full max-h-full object-contain {{ $isOutofStock ? 'grayscale opacity-70' : '' }}">
                                @else
                                    <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                                @endif
                            </a>

                            <div class="p-4 flex flex-col flex-grow bg-white">
                                <p class="text-[9px] text-amber-600 font-black uppercase tracking-widest mb-1">{{ $prod->brand }}</p>
                                <a href="{{ route('product.detail', $prod->id) }}" class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug hover:text-amber-600 transition-colors mb-4 h-10">
                                    {{ $prod->name }}
                                </a>
                                
                                <div class="mt-auto pt-3 border-t border-slate-100 flex justify-between items-center gap-3">
                                    <p class="font-black text-slate-900 text-base">Rp {{ number_format($price->price ?? 0, 0, ',', '.') }}</p>
                                    
                                    @if(!$isOutofStock)
                                        @auth
                                        {{-- 🚀 BUG FIXED: Tombol keranjang sekarang murni melakukan POST tambah ke keranjang --}}
                                        <form action="{{ route('cart.add', $prod->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Tambah ke Keranjang" class="w-8 h-8 rounded-xl bg-amber-500 text-slate-900 flex items-center justify-center hover:bg-amber-600 transition-all shadow-sm font-bold text-xs">
                                                <i class="fa-solid fa-cart-plus text-xs"></i>
                                            </button>
                                        </form>
                                        @else
                                        <a href="{{ route('login') }}" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                            <i class="fa-solid fa-cart-plus text-xs"></i>
                                        </a>
                                        @endauth
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-16 text-center rounded-3xl border border-slate-200 bg-white">
                            <i class="fa-solid fa-box-open text-5xl text-slate-300 mb-4"></i>
                            <p class="text-sm text-slate-500 font-bold">Produk tidak ditemukan.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination Box --}}
                    <div id="pagination-box" class="mt-12 mb-6 flex justify-center customer-pagination-container">
                        {{ $products->appends(['search' => $search])->links() }}
                    </div>

                </div>
            </div>
        </main>
    </div>

    {{-- 🤖 TRIGGER TRIGGER: BUTTON MELAYANG MEKANIK AI --}}
    <div class="floating-ai-btn" onclick="openAiOverlay()"><i class="fa-solid fa-robot"></i></div>

    {{-- 🤖 OVERLAY PANEL CHATBOX MEKANIK AI --}}
    <div id="ai-chat-overlay" class="flex flex-col">
        <div class="p-4 border-b border-slate-100 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-t-2xl flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-robot"></i>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider">Mekanik AI</h4>
                    <p class="text-[9px] text-indigo-100 font-medium">Konsultasi Kerusakan Motor</p>
                </div>
            </div>
            <button onclick="closeAiOverlay()" class="text-indigo-100 hover:text-white text-sm"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        {{-- Tempat Chat History Box --}}
        <div id="ai-chat-body" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 text-xs">
            <div class="bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-2xl p-3 leading-relaxed">
                Halo bos! Ada keluhan apa dengan motornya hari ini? Ketik keluhanmu (misal: "mesin cepat panas" atau "rem bunyi"), nanti saya carikan penyebab dan suku cadang yang cocok!
            </div>
        </div>

        {{-- Input Box --}}
        <div class="p-3 border-t border-slate-100 bg-white flex gap-2">
            <input type="text" id="ai-chat-message" onkeypress="handleAiKeyPress(event)" placeholder="Ketik keluhan motor disini..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-indigo-500 focus:bg-white transition-all">
            <button onclick="sendAiMessageOverlay()" class="bg-indigo-600 hover:bg-indigo-700 text-white w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-paper-plane text-xs"></i></button>
        </div>
    </div>

    <script>
        // System Live Search Biasa (SQL LIKE)
        const searchInput = document.getElementById('ai-search-input');
        const loadingIcon = document.getElementById('ai-loading');
        const productsGridContainer = document.getElementById('products-grid-container');

        // 🤖 JavaScript Handler untuk Overlay Chatbox Mekanik AI
        function openAiOverlay() { document.getElementById('ai-chat-overlay').classList.add('open'); }
        function closeAiOverlay() { document.getElementById('ai-chat-overlay').classList.remove('open'); }
        
        function handleAiKeyPress(e) { if(e.key === 'Enter') sendAiMessageOverlay(); }

        async function sendAiMessageOverlay() {
            const input = document.getElementById('ai-chat-message');
            const msg = input.value.trim();
            if(!msg) return;

            const chatBody = document.getElementById('ai-chat-body');
            
            // Render Chat User
            chatBody.insertAdjacentHTML('beforeend', `<div class="bg-white border border-slate-200 text-slate-800 rounded-2xl p-3 ml-8 text-right font-semibold">${msg}</div>`);
            input.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Render Loading AI
            const loadId = 'load-' + Date.now();
            chatBody.insertAdjacentHTML('beforeend', `<div id="${loadId}" class="text-slate-400 font-medium italic"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Mekanik sedang menganalisis mesin...</div>`);

            try {
                const res = await fetch("{{ route('customer.ai-chat.send') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ message: msg })
                });
                const data = await res.json();
                document.getElementById(loadId).remove();

                if(data.status === 'success') {
                    chatBody.insertAdjacentHTML('beforeend', `<div class="bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-2xl p-3 leading-relaxed">${data.reply}</div>`);
                } else {
                    chatBody.insertAdjacentHTML('beforeend', `<div class="bg-rose-50 text-rose-600 border border-rose-100 rounded-2xl p-3">Maaf bos, terjadi kendala koneksi ke server AI.</div>`);
                }
            } catch(e) {
                document.getElementById(loadId).remove();
                chatBody.insertAdjacentHTML('beforeend', `<div class="bg-rose-50 text-rose-600 border border-rose-100 rounded-2xl p-3">Koneksi ke server terputus.</div>`);
            }
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    </script>
</body>
</html>