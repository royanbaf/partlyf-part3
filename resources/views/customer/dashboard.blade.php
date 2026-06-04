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
        main::-webkit-scrollbar { width: 4px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .glass-header { 
            background: #ffffff;
            border-b: 1px solid #e2e8f0; 
        }
        
        .luxury-card {
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .luxury-card:hover {
            border-color: #c5a880; 
            transform: translateY(-2px); 
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.03);
        }

        .category-btn-active {
            background-color: #f4f4f5 !important;
            color: #121212 !important;
            font-weight: 700;
        }

        .customer-pagination-container nav > div:first-child { display: none; } 
        .customer-pagination-container nav > div:last-child { display: flex; justify-content: center; width: 100%; gap: 6px; }
        .customer-pagination-container .page-link, .customer-pagination-container span[aria-current="page"] > span, .customer-pagination-container nav span.relative {
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px !important; font-size: 12px; font-weight: 600; transition: all 0.2s ease;
        }
        .customer-pagination-container a.page-link, .customer-pagination-container span.page-link { background-color: #ffffff !important; color: #64748b !important; border: 1px solid #e2e8f0 !important; }
        .customer-pagination-container span[aria-current="page"] > span { background-color: #121212 !important; color: #ffffff !important; border: 1px solid #121212 !important; }

        #ai-chat-overlay {
            position: fixed; bottom: -600px; right: 30px; width: 380px; height: 500px;
            background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
            border-radius: 16px 16px 0 0; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); z-index: 2000;
        }
        #ai-chat-overlay.open { bottom: 0; }
        .floating-ai-btn {
            position: fixed; bottom: 30px; right: 30px; width: 54px; height: 54px;
            background: #121212; color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; cursor: pointer; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); z-index: 1999;
            transition: all 0.3s;
        }
        .floating-ai-btn:hover { transform: scale(1.05); color: #c5a880; }

        /* Custom Premium Slider Input */
        .premium-range-input {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            outline: none;
        }
        .premium-range-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #c5a880;
            cursor: pointer;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.1s;
        }
        .premium-range-input::-webkit-slider-thumb:active {
            transform: scale(1.2);
        }
    </style>
</head>

<body class="bg-[#f9fafb] font-sans text-slate-700 h-screen overflow-hidden flex">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">

        {{-- TOP HEADER BAR --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            
            <div class="relative w-full max-w-2xl flex-grow z-[100] flex items-center gap-4">
                
                <button type="button" onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-900 transition-colors w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100/60 flex-shrink-0">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                <form id="ai-search-form" action="{{ route('customer.dashboard') }}" method="GET" class="relative flex-1">
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text" name="search" id="ai-search-input" value="{{ $search ?? '' }}" 
                                placeholder="Cari suku cadang asli, ketik nama atau tipe kendaraan..." autocomplete="off"
                                class="w-full bg-[#f4f4f5] border border-transparent rounded-xl py-2.5 pl-11 pr-12 focus:bg-white focus:border-gray-300 transition-all text-sm text-slate-900 placeholder-slate-400 outline-none focus:ring-0">
                            
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                        </div>
                        <button type="submit" class="bg-[#121212] hover:bg-[#c5a880] text-white text-xs font-bold uppercase tracking-wider px-6 py-3 rounded-xl transition-all flex-shrink-0">
                            Search
                        </button>
                    </div>

                    <div id="ai-loading" class="absolute right-24 top-1/2 -translate-y-1/2 hidden">
                        <i class="fa-solid fa-circle-notch fa-spin text-slate-400 text-xs"></i>
                    </div>
                </form>
            </div>
            
            <div class="flex items-center gap-4 ml-8 flex-shrink-0">
                {{-- 🚀 REKOMENDASI DOSEN FIXED: Indikator Lonceng Promo Aktif Tanpa Mengunci user_id --}}
                <a href="{{ Auth::check() ? route('customer.broadcast') : route('login') }}" class="relative text-slate-400 hover:text-slate-900 w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50">
                    <i class="fa-solid fa-bell text-base"></i>
                    @if(isset($hasNotification) && $hasNotification)
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-[0_0_6px_rgba(244,63,94,0.5)]"></span>
                    @elseif(\App\Models\Broadcast::where('type', 'promo')->where('is_read', false)->exists())
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-[0_0_6px_rgba(244,63,94,0.5)]"></span>
                    @endif
                </a>

                <a href="{{ Auth::check() ? route('customer.wishlist') : route('login') }}" class="relative text-slate-400 hover:text-rose-500 w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50">
                    <i class="fa-solid fa-heart text-base"></i>
                </a>
                <a href="{{ Auth::check() ? route('customer.cart') : route('login') }}" class="relative text-slate-400 hover:text-slate-900 w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50">
                    <i class="fa-solid fa-cart-shopping text-base"></i>
                </a>
                <div class="h-4 w-px bg-slate-200"></div>
                @auth
                <a href="{{ route('customer.profile') }}" class="w-9 h-9 bg-slate-900 text-white rounded-full flex items-center justify-center font-bold text-xs border border-gray-100 shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </a>
                @else
                <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-900 hover:text-white px-4 py-2 rounded-xl transition-all">Sign In</a>
                @endauth
            </div>
        </header>

        {{-- Main Area Container --}}
        <main class="flex-1 overflow-y-auto p-8 bg-[#f9fafb]">
            <div class="max-w-[1440px] mx-auto space-y-6">

                {{-- BANNER USER PROFILE --}}
                <div id="hero-profile-banner" class="p-6 rounded-2xl bg-white border border-slate-200 flex flex-wrap gap-6 justify-between items-center shadow-none">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-slate-900 text-white text-base font-bold flex items-center justify-center">
                            {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-400 font-medium">Selamat datang kembali,</p>
                            <h2 class="text-base font-bold text-slate-900 leading-tight">{{ Auth::user()->name ?? 'Pelanggan Eceran' }}</h2>
                            <p class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5"><i class="fa-solid fa-coins text-[#c5a880]"></i> {{ Auth::user()->loyalty_points ?? 0 }} partlyfe points</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('customer.cart') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white flex items-center gap-2 hover:bg-slate-50 transition-all text-xs font-semibold text-slate-700">
                            <i class="fa-solid fa-basket-shopping text-slate-400"></i> Keranjang
                        </a>
                        <a href="{{ route('customer.transactions') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white flex items-center gap-2 hover:bg-slate-50 transition-all text-xs font-semibold text-slate-700">
                            <i class="fa-solid fa-receipt text-slate-400"></i> Riwayat Trx
                        </a>
                        <button type="button" onclick="openAiOverlay()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white flex items-center gap-2 hover:bg-slate-50 transition-all text-xs font-semibold text-[#c5a880]">
                            <i class="fa-solid fa-robot"></i> Mekanik AI
                        </button>
                    </div>
                </div>

                {{-- DUAL COLUMN LAYOUT --}}
                <div id="main-catalog-section" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <aside class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-6 space-y-6">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Kategori Suku Cadang</h3>
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('customer.dashboard') }}" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-all {{ !$search ? 'category-btn-active' : '' }}">
                                    Semua Suku Cadang
                                </a>
                                @foreach($categories as $cat)
                                <a href="{{ route('customer.dashboard', ['search' => $cat->name]) }}" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-all {{ isset($search) && strtolower($search) === strtolower($cat->name) ? 'category-btn-active' : '' }}">
                                    {{ $cat->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-5 border-t border-slate-100 space-y-4">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Harga Maksimum</h3>
                            
                            <div class="relative pt-7 pb-1">
                                <div id="price-bubble" class="absolute top-0 bg-[#121212] text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-md -translate-x-1/2 after:content-[''] after:absolute after:top-full after:left-1/2 after:-translate-x-1/2 after:border-4 after:border-transparent after:border-t-[#121212] whitespace-nowrap transition-all duration-75">
                                    Rp 2.000.000
                                </div>
                                <input type="range" id="price-slider" min="10000" max="2000000" step="10000" value="2000000" class="premium-range-input">
                            </div>
                            
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold">
                                <span>Rp 10.000</span>
                                <span>Rp 2.000.000</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <span onclick="setSliderValue(50000)" class="bg-[#f4f4f5] hover:bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1.5 rounded-lg cursor-pointer transition-all">Under 50rb</span>
                                <span onclick="setSliderValue(150000)" class="bg-[#f4f4f5] hover:bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1.5 rounded-lg cursor-pointer transition-all">Under 150rb</span>
                                <span onclick="setSliderValue(500000)" class="bg-[#f4f4f5] hover:bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1.5 rounded-lg cursor-pointer transition-all">Under 500rb</span>
                            </div>
                        </div>
                    </aside>

                    <div class="lg:col-span-9 space-y-4">
                        <div class="flex justify-between items-center bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-xs font-semibold shadow-none">
                            <p class="text-slate-500">
                                Menampilkan hasil komponen presisi <span class="text-[#c5a880] font-bold">"{{ $search ?? 'Semua Suku Cadang' }}"</span> ({{ $products->total() }} SKU)
                            </p>
                        </div>

                        {{-- CONTAINER GRID KARTU PRODUK --}}
                        <div id="products-grid-container" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                            @forelse($products as $prod)
                            @php 
                                $retailPrice = $prod->prices->where('price_level', 1)->first();
                                $displayPrice = $retailPrice->price ?? 0;
                                // Diskon khusus B2B: potongan harga Rp 5.000
                                if (Auth::check() && strtolower(Auth::user()->role) === 'b2b') {
                                    $displayPrice = max(0, $displayPrice - 5000);
                                }
                                $isOutofStock = $prod->current_stock <= 0;
                            @endphp
                            
                            <div class="luxury-card flex flex-col overflow-hidden relative {{ $isOutofStock ? 'opacity-70' : '' }}" data-price="{{ $displayPrice }}">
                                @if($isOutofStock)
                                <div class="absolute top-2.5 left-2.5 bg-slate-900 text-white text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 z-20">Stok Habis</div>
                                @elseif($prod->cashback_percent > 0)
                                <div class="absolute top-2.5 left-2.5 bg-[#c5a880] text-white text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 z-20">Cashback {{ $prod->cashback_percent }}%</div>
                                @endif

                                <a href="{{ route('product.detail', $prod->id) }}" class="h-44 bg-[#f4f4f5] flex items-center justify-center p-4 border-b border-slate-100 relative overflow-hidden group">
                                    @if($prod->images && $prod->images->isNotEmpty())
                                        <img src="{{ asset('storage/products/' . basename(optional($prod->images->first())->image_path ?? 'default.png')) }}" alt="{{ $prod->name }}"
                                            onerror="this.onerror=null; this.src='https://placehold.co/300x300/f4f4f5/c5a880?text=Foto+Menyusul';"
                                            class="max-w-full max-h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300 {{ $isOutofStock ? 'grayscale' : '' }}">
                                    @else
                                        <i class="fa-solid fa-box-open text-2xl text-slate-300"></i>
                                    @endif
                                </a>

                                <div class="p-4 flex flex-col flex-grow bg-white">
                                    <p class="font-extrabold text-slate-900 text-sm sm:text-base leading-none mb-1">
                                        @if(Auth::check() && Auth::user()->role === 'B2B')
                                            <span class="text-[10px] text-emerald-500 font-bold">DISKON B2B Rp 5.000</span><br>
                                        @endif
                                        Rp {{ number_format($displayPrice, 0, ',', '.') }}
                                    </p>
                                    
                                    <a href="{{ route('product.detail', $prod->id) }}" class="text-xs font-semibold text-slate-700 line-clamp-2 leading-snug hover:text-[#c5a880] transition-colors mb-3 h-9">
                                        {{ $prod->name }}
                                    </a>
                                    
                                    <div class="mt-auto pt-2 border-t border-slate-50 flex justify-between items-center gap-2">
                                        <div class="flex items-center gap-1 text-[10px] font-bold text-amber-500 bg-amber-500/5 px-2 py-0.5 rounded">
                                            <i class="fa-solid fa-star text-[9px]"></i> 5.0
                                        </div>
                                        
                                        @if(!$isOutofStock)
                                            @auth
                                            <form action="{{ route('cart.add', $prod->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" title="Tambah ke Keranjang" class="w-7 h-7 rounded-full bg-slate-900 hover:bg-[#c5a880] text-white flex items-center justify-center transition-all shadow-none">
                                                    <i class="fa-solid fa-cart-plus text-[9px]"></i>
                                                </button>
                                            </form>
                                            @else
                                            <a href="{{ route('login') }}" class="w-7 h-7 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#c5a880] hover:text-white transition-all border border-gray-100">
                                                <i class="fa-solid fa-cart-plus text-[9px]"></i>
                                            </a>
                                            @endauth
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full py-20 text-center rounded-2xl border border-slate-200 bg-white">
                                <i class="fa-solid fa-box-open text-3xl text-slate-300 mb-3"></i>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Komponen tidak ditemukan.</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Pagination Box --}}
                        <div id="pagination-box" class="mt-10 flex justify-center customer-pagination-container">
                            {{ $products->appends(['search' => $search])->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- FLOATING AI BUTTON --}}
    <div class="floating-ai-btn" onclick="openAiOverlay()"><i class="fa-solid fa-robot"></i></div>

    {{-- OVERLAY PANEL CHATBOX MEKANIK AI --}}
    <div id="ai-chat-overlay" class="flex flex-col">
        <div class="p-4 border-b border-slate-100 bg-[#121212] text-white rounded-t-xl flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-robot text-[#c5a880]"></i>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider">Rekomendasi Pintar</h4>
                    <p class="text-[9px] text-gray-400 font-medium">Asisten Masalah Motor</p>
                </div>
            </div>
            <button onclick="closeAiOverlay()" class="text-gray-400 hover:text-white text-sm"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div id="ai-chat-body" class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#fbfbfa] text-xs">
            <div class="bg-[#f5f2eb] border border-[#c5a880]/10 text-gray-800 rounded-xl p-3 leading-relaxed font-medium">
                Halo bos! Ada keluhan apa dengan motornya hari ini? Ketik keluhanmu (misal: "mesin cepat panas" atau "rem bunyi"), nanti saya carikan penyebab dan suku cadang yang cocok!
            </div>
        </div>

        <div class="p-3 border-t border-slate-100 bg-white flex gap-2">
            <input type="text" id="ai-chat-message" onkeypress="handleAiKeyPress(event)" placeholder="Ketik keluhan motor disini..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-[#c5a880] focus:bg-white transition-all text-gray-800 shadow-none focus:ring-0">
            <button onclick="sendAiMessageOverlay()" class="bg-slate-900 hover:bg-[#c5a880] text-white w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors"><i class="fa-solid fa-paper-plane text-[10px]"></i></button>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('ai-search-input');
        const loadingIcon = document.getElementById('ai-loading');
        const productsGridContainer = document.getElementById('products-grid-container');
        const priceSlider = document.getElementById('price-slider');
        const priceBubble = document.getElementById('price-bubble');

        function filterProductsByPrice() {
            if(!priceSlider) return;
            const maxPrice = parseInt(priceSlider.value);
            const productCards = document.querySelectorAll('.luxury-card');
            let visibleCount = 0;

            productCards.forEach(card => {
                const productPrice = parseInt(card.getAttribute('data-price')) || 0;
                if (productPrice <= maxPrice) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            let emptyState = document.getElementById('client-empty-state');
            if (visibleCount === 0 && productCards.length > 0) {
                if (!emptyState) {
                    productsGridContainer.insertAdjacentHTML('afterend', `
                        <div id="client-empty-state" class="col-span-full py-20 text-center rounded-2xl border border-slate-200 bg-white w-full mt-4">
                            <i class="fa-solid fa-box-open text-3xl text-gray-200 mb-3"></i>
                            <p class="text-xs text-gray-400 font-semibold tracking-wider uppercase">Tidak ada komponen dalam rentang harga ini.</p>
                        </div>
                    `);
                }
            } else {
                if (emptyState) emptyState.remove();
            }
        }

        function updatePriceBubble() {
            if(!priceSlider || !priceBubble) return;
            const val = priceSlider.value;
            const min = priceSlider.min ? priceSlider.min : 0;
            const max = priceSlider.max ? priceSlider.max : 100;
            const percent = Number(((val - min) * 100) / (max - min));
            
            priceBubble.innerHTML = "Rp " + Number(val).toLocaleString('id-ID');
            priceBubble.style.left = `calc(${percent}% + (${8 - percent * 0.15}px))`;
            
            filterProductsByPrice();
        }

        function setSliderValue(val) {
            if(priceSlider) {
                priceSlider.value = val;
                updatePriceBubble();
            }
        }

        if(priceSlider) {
            priceSlider.addEventListener('input', updatePriceBubble);
            window.addEventListener('DOMContentLoaded', updatePriceBubble);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            if (sidebar) {
                if (sidebar.classList.contains('w-72')) {
                    sidebar.classList.remove('w-72', 'px-4');
                    sidebar.classList.add('w-0', 'overflow-hidden', 'border-r-0');
                    localStorage.setItem('sidebarState', 'hidden');
                } else {
                    sidebar.classList.remove('w-0', 'overflow-hidden', 'border-r-0');
                    sidebar.classList.add('w-72', 'px-4');
                    localStorage.setItem('sidebarState', 'visible');
                }
            }
        }

        function openAiOverlay() { document.getElementById('ai-chat-overlay').classList.add('open'); }
        function closeAiOverlay() { document.getElementById('ai-chat-overlay').classList.remove('open'); }
        function handleAiKeyPress(e) { if(e.key === 'Enter') sendAiMessageOverlay(); }

        async function sendAiMessageOverlay() {
            const input = document.getElementById('ai-chat-message');
            const msg = input.value.trim();
            if(!msg) return;

            const chatBody = document.getElementById('ai-chat-body');
            chatBody.insertAdjacentHTML('beforeend', `<div class="bg-white border border-gray-200 text-gray-800 rounded-xl p-3 ml-8 text-right font-semibold shadow-none">${msg}</div>`);
            input.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            const loadId = 'load-' + Date.now();
            chatBody.insertAdjacentHTML('beforeend', `<div id="${loadId}" class="text-gray-400 font-medium italic text-[11px]"><i class="fa-solid fa-circle-notch fa-spin mr-1.5 text-[#c5a880]"></i>Mekanik sedang menganalisis mesin...</div>`);

            try {
                const res = await fetch("{{ route('customer.ai-chat.send') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ message: msg })
                });
                const data = await res.json();
                document.getElementById(loadId).remove();

                if(data.status === 'success') {
                    chatBody.insertAdjacentHTML('beforeend', `<div class="bg-[#f5f2eb] border border-[#c5a880]/10 text-gray-800 rounded-xl p-3 leading-relaxed font-medium">${data.reply}</div>`);
                } else {
                    chatBody.insertAdjacentHTML('beforeend', `<div class="bg-rose-50 text-rose-600 border border-rose-100 rounded-xl p-3">Maaf bos, terjadi kendala koneksi ke server AI.</div>`);
                }
            } catch(e) {
                document.getElementById(loadId).remove();
                chatBody.insertAdjacentHTML('beforeend', `<div class="bg-rose-50 text-rose-600 border border-rose-100 rounded-xl p-3">Koneksi ke server terputus.</div>`);
            }
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    </script>
</body>
</html>