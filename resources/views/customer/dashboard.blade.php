<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Suku Cadang Presisi | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- 🚀 SDK Midtrans Snap Sandbox --}}
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        main::-webkit-scrollbar { width: 6px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes blobFloat {
            0%   { transform: translate(0px, 0px) scale(1); }
            33%  { transform: translate(30px, -50px) scale(1.02); }
            66%  { transform: translate(-20px, 20px) scale(0.98); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; will-change: transform; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(245,158,11,0.06) 0%, transparent 70%); top: -100px; left: -100px; animation: blobFloat 15s ease-in-out infinite; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(99,102,241,0.04) 0%, transparent 70%); bottom: -100px; right: -100px; animation: blobFloat 20s ease-in-out infinite reverse; }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-b: 1px solid rgba(226, 232, 240, 0.8); 
        }
        
        .luxury-card {
            background: #ffffff; border: 1px solid #e5e7eb; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .luxury-card:hover {
            border-color: rgba(245,158,11,0.3); transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(245,158,11,0.08), 0 1px 2px rgba(255,255,255,1) inset;
        }

        .category-btn-active {
            background: #fffbeb !important; border-color: #f59e0b !important; color: #b45309 !important; font-weight: 700; box-shadow: 0 4px 12px rgba(245,158,11,0.08);
        }

        .customer-pagination-container nav > div:first-child { display: none; } 
        .customer-pagination-container nav > div:last-child { display: flex; justify-content: center; width: 100%; gap: 6px; }
        .customer-pagination-container .page-link, .customer-pagination-container span[aria-current="page"] > span, .customer-pagination-container nav span.relative {
            width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 12px !important; font-size: 13px; font-weight: 700; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(148, 163, 184, 0.05);
        }
        .customer-pagination-container a.page-link, .customer-pagination-container span.page-link { background-color: #ffffff !important; color: #64748b !important; border: 1px solid #e2e8f0 !important; }
        .customer-pagination-container a.page-link:hover { border-color: #f59e0b !important; color: #d97706 !important; background-color: #fffbeb !important; }
        .customer-pagination-container span[aria-current="page"] > span { background-color: #f59e0b !important; color: #ffffff !important; border: 1px solid #f59e0b !important; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2) !important; }
        .customer-pagination-container a[rel="next"], .customer-pagination-container a[rel="prev"] { font-size: 10px; color: #f59e0b !important; }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex selection:bg-amber-100 selection:text-amber-900">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">

        {{-- Header / Search Bar --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            
            {{-- SEARCH BAR ADVANCED + AI DIRECT UPDATE --}}
            <div class="relative w-full max-w-3xl flex-grow z-[100]">
                <form id="ai-search-form" action="{{ route('customer.dashboard') }}" method="GET" class="relative">
                    <input type="text" name="search" id="ai-search-input" value="{{ $search ?? '' }}" 
                        placeholder="Ketik keluhan motor (Misal: 'Tarikan berat' atau 'Rem blong')..." autocomplete="off"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-12 pr-12 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all text-sm text-slate-800 placeholder-slate-400 outline-none shadow-sm">
                    
                    <button type="submit" class="absolute left-4 top-2.5 text-slate-400 hover:text-amber-600 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <div id="ai-loading" class="absolute right-4 top-3 hidden">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-500 text-lg"></i>
                    </div>
                </form>
            </div>
            
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

        {{-- Main Dashboard Content --}}
        <main class="flex-1 overflow-y-auto p-8 relative">
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
                        @foreach([
                            ['fa-basket-shopping', route('customer.cart'), (\App\Models\Cart::where('user_id', Auth::id())->count() ?? 0) . ' Barang', 'Keranjang'],
                            ['fa-receipt', route('customer.transactions'), (\App\Models\Transaction::where('user_id', Auth::id())->count() ?? 0) . ' Transaksi', 'Riwayat'],
                            ['fa-robot', route('customer.ai-chat'), 'Tanya AI', 'Mekanik']
                        ] as $info)
                        <a href="{{ $info[1] }}" class="p-4 rounded-2xl border border-slate-100 bg-slate-50 flex flex-col items-center text-center w-28 hover:border-amber-200 hover:bg-amber-50 transition-all group">
                            <i class="fa-solid {{ $info[0] }} text-xl text-amber-500 mb-2.5 transition-transform group-hover:scale-110"></i>
                            <p class="text-[11px] font-black text-slate-900 leading-tight">{{ $info[2] }}</p>
                            <p class="text-[9px] font-medium text-slate-400 mt-0.5">{{ $info[3] }}</p>
                        </a>
                        @endforeach
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
                            <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-500 transition-all hover:border-slate-400 hover:text-slate-800 flex-shrink-0 {{ !$search ? 'category-btn-active' : '' }}">
                                Semua Suku Cadang
                            </a>
                            @foreach($categories as $cat)
                            <a href="{{ route('customer.dashboard', ['search' => $cat->name]) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-500 transition-all hover:border-slate-400 hover:text-slate-800 flex-shrink-0 {{ isset($search) && strtolower($search) === strtolower($cat->name) ? 'category-btn-active' : '' }}">
                                {{ $cat->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info Title Section --}}
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
                            <div class="absolute top-3 left-3 bg-rose-500 text-white text-[9px] font-black px-2.5 py-1 rounded shadow-sm z-20">
                                STOK HABIS
                            </div>
                            @elseif($prod->cashback_percent > 0)
                            <div class="absolute top-3 left-3 bg-rose-500 text-white text-[9px] font-black px-2 py-0.5 rounded z-20">
                                Cashback {{ $prod->cashback_percent }}%
                            </div>
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
                                    
                                    @if($isOutofStock)
                                        @auth
                                        <form action="{{ route('wishlist.toggle', $prod->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Simpan ke Wishlist" class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100">
                                                <i class="fa-solid fa-heart text-xs"></i>
                                            </button>
                                        </form>
                                        @else
                                        <a href="{{ route('login') }}" title="Simpan ke Wishlist" class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100">
                                            <i class="fa-solid fa-heart text-xs"></i>
                                        </a>
                                        @endauth
                                    @else
                                        @auth
                                        {{-- 🚀 LIVE AJAX TRIGGER UNTUK BELI SEKARANG --}}
                                        <button type="button" onclick="beliLangsung('{{ $prod->id }}')" title="Beli Sekarang" class="w-8 h-8 rounded-xl bg-amber-500 text-slate-900 flex items-center justify-center hover:bg-amber-600 transition-all shadow-sm font-bold text-xs">
                                            <i class="fa-solid fa-cart-plus text-xs"></i>
                                        </button>
                                        @else
                                        <a href="{{ route('login') }}" title="Tambah ke Keranjang" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm">
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
                            <p class="text-sm text-slate-500 font-bold">Produk "{{ $search }}" tidak ditemukan.</p>
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

    {{-- 🚀 JAVASCRIPT SYSTEM --}}
    <script>
        const searchInput = document.getElementById('ai-search-input');
        const loadingIcon = document.getElementById('ai-loading');
        
        const catalogTitle = document.getElementById('catalog-title');
        const categoriesFilterBox = document.getElementById('categories-filter-box');
        const productsGridContainer = document.getElementById('products-grid-container');
        const paginationBox = document.getElementById('pagination-box');

        const defaultGridHtml = productsGridContainer.innerHTML;

        let typingTimer;
        const doneTypingInterval = 800; 

        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            const query = searchInput.value.trim();
            
            if (query.length > 3) {
                loadingIcon.classList.remove('hidden');
                typingTimer = setTimeout(() => fetchAiDirectResults(query), doneTypingInterval);
            } else if (query.length === 0) {
                resetDashboard();
            }
        });

        function resetDashboard() {
            loadingIcon.classList.add('hidden');
            categoriesFilterBox.classList.remove('hidden');
            paginationBox.classList.remove('hidden');
            catalogTitle.innerHTML = `Daftar Komponen Presisi <span class="text-slate-600">(Total {{ $products->total() }} SKU)</span>`;
            productsGridContainer.innerHTML = defaultGridHtml;
            searchInput.value = '';
        }

        async function fetchAiDirectResults(query) {
            try {
                const response = await fetch(`{{ route('api.search.ai') }}?q=${encodeURIComponent(query)}`);
                const result = await response.json();
                
                loadingIcon.classList.add('hidden');
                productsGridContainer.innerHTML = ''; 

                if (result.data && result.data.length > 0) {
                    categoriesFilterBox.classList.add('hidden'); 
                    paginationBox.classList.add('hidden'); 
                    catalogTitle.innerHTML = `<i class="fa-solid fa-sparkles text-amber-500 mr-1"></i> Rekomendasi Sinar Jaya Motor untuk Keluhan <span class="text-amber-700 font-bold">"${result.interpreted_as.toUpperCase()}"</span> (${result.data.length} SKU):`;

                    result.data.forEach(item => {
                        const imgHtml = item.image 
                            ? `<img src="${item.image}" class="max-w-full max-h-full object-contain">` 
                            : `<div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-300"><i class="fa-solid fa-box-open text-4xl"></i></div>`;
                        
                        const productCard = `
                            <div class="luxury-card rounded-2xl flex flex-col overflow-hidden relative">
                                <a href="${item.url}" class="h-44 bg-white flex items-center justify-center p-4 border-b border-slate-100 relative overflow-hidden group-hover:scale-105 transition-transform duration-500">
                                    ${imgHtml}
                                </a>
                                <div class="p-4 flex flex-col flex-grow bg-white">
                                    <p class="text-[9px] text-amber-600 font-black uppercase tracking-widest mb-1">${item.brand}</p>
                                    <a href="${item.url}" class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug hover:text-amber-600 transition-colors mb-4 h-10">
                                        ${item.name}
                                    </a>
                                    <div class="mt-auto pt-3 border-t border-slate-100 flex justify-between items-center gap-3">
                                        <p class="font-black text-slate-900 text-base">Rp ${item.price}</p>
                                        <button type="button" onclick="beliLangsung('${item.id}')" title="Beli Sekarang" class="w-8 h-8 rounded-xl bg-amber-500 text-slate-900 flex items-center justify-center hover:bg-amber-600 transition-all shadow-sm font-bold">
                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                        
                        productsGridContainer.insertAdjacentHTML('beforeend', productCard);
                    });
                } else {
                    catalogTitle.innerHTML = `Hasil Tidak Ditemukan`;
                    productsGridContainer.innerHTML = `
                        <div class="col-span-full py-16 text-center rounded-3xl border border-slate-200 bg-white">
                            <i class="fa-solid fa-box-open text-5xl text-slate-300 mb-4"></i>
                            <p class="text-sm text-slate-500 font-bold">Tidak ada komponen suku cadang yang cocok di sistem gudang.</p>
                        </div>`;
                }
            } catch (error) {
                console.error('Error fetching data directly to grid layout:', error);
                loadingIcon.classList.add('hidden');
            }
        }

        // =========================================================================
        // 🚀 LIVE CHECKOUT INTERCEPTOR (MENGGUNAKAN REGISTERED ROUTE NAME ASLI)
        // =========================================================================
        async function beliLangsung(productId) {
            try {
                document.body.style.cursor = 'wait';

                const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';

                if (!csrfToken) {
                    alert('Sistem Keamanan: CSRF Token tidak ditemukan di halaman head!');
                    document.body.style.cursor = 'default';
                    return;
                }

                // 🔥 PERBAIKAN UTAMA: Menembak langsung Route Ber-nama bawaan Laravel secara aman
                const response = await fetch("{{ route('customer.payment.initiate') }}", { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        qty: 1
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server Laravel Error Response:', errorText);
                    alert(`Server Menolak (Status HTTP ${response.status}). Pastikan Anda telah login dengan akun b2c.`);
                    document.body.style.cursor = 'default';
                    return;
                }

                const result = await response.json();
                document.body.style.cursor = 'default';

                if (result.status === 'success' && result.snap_token) {
                    // Panggil Pop-up SDK Snap Midtrans
                    window.snap.pay(result.snap_token, {
                        onSuccess: function(paymentResult) {
                            window.location.href = "{{ route('customer.transactions') }}";
                        },
                        onPending: function(paymentResult) {
                            window.location.href = "{{ route('customer.transactions') }}";
                        },
                        onError: function(paymentResult) {
                            window.location.href = "{{ route('customer.transactions') }}";
                        },
                        onClose: function() {
                            window.location.href = "{{ route('customer.transactions') }}";
                        }
                    });
                } else {
                    alert('Respons Gagal: ' + (result.message || 'Token pembayaran tidak valid dari server.'));
                }

            } catch (e) {
                document.body.style.cursor = 'default';
                console.error('Detail Error Fatal JavaScript:', e);
                alert('Terjadi kesalahan fatal saat menghubungkan transaksi ke server.');
            }
        }
    </script>
</body>
</html>