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
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-[#020617] font-sans text-slate-200 h-screen overflow-hidden flex selection:bg-amber-500 selection:text-slate-900">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <div class="absolute -top-40 right-0 w-[500px] h-[500px] bg-rose-600/20 rounded-full filter blur-[150px] pointer-events-none z-0"></div>

        <header class="h-20 glass-panel flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0 border-b border-white/5 shadow-sm">
            <form action="{{ route('customer.dashboard') }}" method="GET" class="relative w-full max-w-3xl flex-grow">
                <input type="text" name="search" placeholder="Cari sparepart lain di Partlyfe..."
                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl py-2.5 pl-12 pr-6 focus:bg-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm text-white placeholder-slate-500">
                <button type="submit" class="absolute left-4 top-2.5 text-slate-400 hover:text-amber-500 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <div class="flex items-center gap-6 ml-8">
                <a href="{{ Auth::check() ? route('customer.wishlist') : route('login') }}"
                    class="relative text-slate-400 hover:text-rose-400 transition cursor-pointer">
                    <i class="fa-solid fa-heart text-xl"></i>
                </a>

                <a href="{{ Auth::check() ? route('customer.cart') : route('login') }}"
                    class="relative text-slate-400 hover:text-amber-400 transition cursor-pointer">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                </a>

                <div class="h-8 w-px bg-white/10"></div>
                @auth
                <div class="w-8 h-8 bg-amber-500 text-slate-900 rounded-full flex items-center justify-center font-bold text-sm shadow-[0_0_10px_rgba(245,158,11,0.5)]">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                @else
                <a href="{{ route('login') }}"
                    class="text-xs font-bold text-amber-400 bg-amber-500/10 px-4 py-2 rounded-full border border-amber-500/30 hover:bg-amber-500 hover:text-slate-900 transition">Masuk</a>
                @endauth
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 scrollbar-hide relative z-10">
            <div class="max-w-[1200px] mx-auto">

                <nav class="text-xs font-medium text-slate-400 mb-8 flex items-center gap-2">
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-amber-400 transition">Beranda</a>
                    <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
                    <span class="text-white font-bold">{{ $product->name }}</span>
                </nav>

                <div class="flex gap-8 items-start">

                    <div class="w-[350px] flex-shrink-0 sticky top-4">
                        <div class="glass-card rounded-3xl aspect-square flex items-center justify-center relative overflow-hidden group border border-white/10 shadow-lg">
                            <div class="w-full h-full flex items-center justify-center bg-slate-900/60 relative overflow-hidden rounded-2xl">
                                @if($product->images && $product->images->isNotEmpty())
                                <img src="{{ asset('storage/products/' . basename(optional($product->images->first())->image_path ?? 'default.png')) }}"
                                    alt="{{ $product->name }}"
                                    onerror="this.onerror=null; this.src='https://placehold.co/400x400/0f172a/f59e0b?text=Foto+Menyusul';"
                                    class="w-full h-full object-contain p-6">
                                @else
                                <i class="fa-solid fa-box-open text-9xl text-slate-700 group-hover:text-slate-500 transition-colors duration-500"></i>
                                @endif
                            </div>
                            <div class="absolute top-4 left-4 bg-amber-500/20 backdrop-blur-md text-amber-400 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest border border-amber-500/30 shadow-lg">
                                100% Original
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow min-w-0 pr-4">
                        <h1 class="text-3xl font-black text-white leading-tight mb-3">{{ $product->name }}</h1>
                        <div class="flex items-center gap-4 mb-6 text-sm">
                            <div class="flex items-center gap-1 text-amber-400 font-bold"><i class="fa-solid fa-star"></i> 4.9</div>
                            <span class="text-slate-600">|</span>
                            <p class="text-slate-400">Terjual <span class="font-bold text-white">1.2 rb+</span></p>
                        </div>
                        @php $retailPrice = $product->prices->where('price_level', 1)->first(); @endphp
                        <h2 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-rose-400 mb-8">
                            Rp {{ number_format($retailPrice->price ?? 0, 0, ',', '.') }}
                        </h2>

                        <div class="glass-card rounded-2xl p-6 mb-8 border border-white/5">
                            <h3 class="font-bold text-white mb-4 text-lg">Detail Produk</h3>
                            <div class="space-y-4 text-sm">
                                <div class="flex border-b border-white/5 pb-3">
                                    <p class="w-40 text-slate-400 font-medium">Merk</p>
                                    <p class="font-bold text-amber-400">{{ $product->brand }}</p>
                                </div>
                                <div class="flex border-b border-white/5 pb-3">
                                    <p class="w-40 text-slate-400 font-medium">Kategori</p>
                                    <p class="font-semibold text-white">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </p>
                                </div>
                                <div class="flex">
                                    <p class="w-40 text-slate-400 font-medium">SKU Part</p>
                                    <p class="font-mono font-bold bg-white/10 px-2 rounded text-amber-300 text-xs py-0.5 border border-white/10">
                                        {{ $product->item_code }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card rounded-2xl p-6 border border-white/5">
                            <h3 class="font-bold text-white mb-3 text-lg">Deskripsi</h3>
                            <p class="text-slate-400 leading-relaxed text-sm whitespace-pre-line">
                                Suku cadang original {{ $product->brand }} dengan ketahanan maksimal.
                                <br><br>⚠️ <span class="text-amber-400">Gunakan Mekanik AI kami jika ragu kompatibilitas part ini.</span>
                            </p>
                        </div>
                    </div>

                    <div class="w-[320px] flex-shrink-0 sticky top-4">
                        <div class="glass-panel rounded-3xl p-6 shadow-2xl relative overflow-hidden border border-white/5">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent pointer-events-none"></div>

                            @php $isOutofStock = $product->current_stock <= 0; @endphp 
                            
                            @if($isOutofStock) 
                            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm font-bold px-4 py-4 rounded-xl mb-6 flex items-start gap-3 backdrop-blur-sm">
                                <i class="fa-solid fa-circle-exclamation mt-1 text-lg"></i>
                                <span class="leading-relaxed">Stok sedang habis! Masukkan ke Wishlist untuk mendapat notif saat restock.</span>
                            </div>

                            <h3 class="font-bold text-slate-500 mb-4">Mulai Transaksi</h3>
                            <div class="space-y-3">
                                <button disabled class="w-full bg-slate-800/40 text-slate-500 font-black py-3 rounded-xl cursor-not-allowed border border-white/5 flex justify-center items-center gap-2 shadow-inner">
                                    <i class="fa-solid fa-cart-arrow-down"></i> Tidak Bisa Dibeli
                                </button>
                            </div>
                            @else
                                @auth
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <h3 class="font-bold text-white mb-4">Atur Jumlah</h3>
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="flex items-center bg-slate-900/50 border border-white/10 rounded-xl p-1">
                                            <button type="button" onclick="decreaseQty()" class="w-10 h-10 rounded-lg hover:bg-white/10 text-slate-300 font-bold">-</button>
                                            
                                            <span id="qtyDisplay" class="w-12 text-center font-bold text-white flex items-center justify-center select-none cursor-default">1</span>
                                            
                                            <input type="hidden" id="qtyInput" name="qty" value="1">
                                            
                                            <button type="button" onclick="increaseQty({{ $product->current_stock }})" class="w-10 h-10 rounded-lg hover:bg-white/10 text-amber-400 font-bold">+</button>
                                        </div>
                                        <p class="text-xs text-slate-400">Stok: <span class="text-white">{{ $product->current_stock }}</span></p>
                                    </div>
                                    <div class="flex justify-between items-center mb-8 pb-6 border-b border-white/10">
                                        <p class="text-sm text-slate-400">Subtotal</p>
                                        <p class="text-xl font-black text-amber-400" id="subtotalText">Rp {{ number_format($retailPrice->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="space-y-3">
                                        <button type="submit" class="w-full bg-amber-500 text-slate-900 font-black py-3 rounded-xl hover:bg-amber-400 hover:scale-[1.02] transition shadow-[0_0_20px_rgba(245,158,11,0.4)] flex justify-center items-center gap-2">
                                            <i class="fa-solid fa-cart-plus"></i> + Keranjang
                                        </button>
                                        <button type="button" id="btn-beli-langsung" data-product-id="{{ $product->id }}" class="w-full bg-transparent border border-amber-500 text-amber-400 font-bold py-3 rounded-xl hover:bg-amber-500/10 transition flex justify-center items-center gap-2">
                                            <i class="fa-solid fa-bolt"></i> Beli Langsung
                                        </button>
                                    </div>
                                </form>
                                @else
                                <h3 class="font-bold text-white mb-6">Mulai Transaksi</h3>
                                <div class="space-y-3">
                                    <a href="{{ route('login') }}" class="flex w-full bg-amber-500 text-slate-900 font-black py-3 rounded-xl hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.4)] justify-center items-center gap-2">+ Keranjang</a>
                                    <a href="{{ route('login') }}" class="flex w-full bg-transparent border border-amber-500 text-amber-400 font-bold py-3 rounded-xl hover:bg-amber-500/10 justify-center items-center">Beli Langsung</a>
                                </div>
                                @endauth
                            @endif

                            @auth
                            @php $inWishlist = \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists(); @endphp
                            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="mt-6 pt-6 border-t border-white/5">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 text-sm font-bold {{ $inWishlist ? 'text-rose-400' : 'text-slate-400 hover:text-rose-400' }} transition">
                                    <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart text-base"></i>
                                    {{ $inWishlist ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}
                                </button>
                            </form>
                            @else
                            <a href="{{ route('login') }}" class="mt-6 pt-6 border-t border-white/5 flex items-center justify-center gap-2 text-sm font-bold text-slate-400 hover:text-rose-400 transition">
                                <i class="fa-regular fa-heart text-base"></i> Simpan ke Wishlist
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>

                @if(isset($recommendations) && $recommendations->count() > 0)
                <div class="mt-20 pt-10 border-t border-white/10">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-black text-white flex items-center gap-3">
                            <i class="fa-solid fa-robot text-indigo-400"></i> AI Smart Recommendations
                        </h2>
                    </div>

                    <div class="flex gap-4 overflow-x-auto pb-6 no-scrollbar snap-x">
                        @foreach($recommendations as $rec)
                        @php
                        $recPrice = $rec->prices->where('price_level', 1)->first();
                        $recIsOutofStock = $rec->current_stock <= 0; 
                        @endphp 
                        
                        <div class="snap-start flex-shrink-0 w-[200px] glass-card rounded-2xl hover:shadow-[0_0_25px_rgba(245,158,11,0.15)] hover:border-amber-500/50 transition-all duration-300 group flex flex-col relative overflow-hidden {{ $recIsOutofStock ? 'opacity-70 grayscale-[30%]' : '' }}">

                            <div class="absolute top-3 right-3 z-30 opacity-0 group-hover:opacity-100 transition-opacity">
                                @auth
                                @php $recInWishlist = \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $rec->id)->exists(); @endphp
                                <form action="{{ route('wishlist.toggle', $rec->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center transition-all shadow-lg border {{ $recInWishlist ? 'bg-rose-500 text-white border-rose-400' : 'bg-slate-900/80 text-slate-300 border-white/10 hover:bg-rose-500 hover:text-white' }}">
                                        <i class="fa-solid fa-heart text-xs"></i>
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('login') }}" class="w-8 h-8 bg-slate-900/80 text-slate-300 border border-white/10 rounded-full flex items-center justify-center hover:bg-rose-500 hover:text-white transition shadow-lg">
                                    <i class="fa-solid fa-heart text-xs"></i>
                                </a>
                                @endauth
                            </div>

                            <a href="{{ route('product.detail', $rec->id) }}" class="flex flex-col h-full cursor-pointer">
                                <div class="h-32 bg-slate-900/60 flex items-center justify-center relative overflow-hidden border-b border-white/5">
                                    @if($rec->current_stock > 0 && isset($rec->cashback_percent) && $rec->cashback_percent > 0)
                                    <div class="absolute top-2 left-2 bg-rose-500/90 backdrop-blur-sm text-white text-[10px] font-black px-2 py-1 rounded-md z-10 border border-rose-400/50">
                                        Cashback {{ $rec->cashback_percent }}%
                                    </div>
                                    @endif

                                    @if($rec->images && $rec->images->isNotEmpty())
                                    <img src="{{ asset('storage/products/' . basename(optional($rec->images->first())->image_path ?? 'default.png')) }}"
                                        alt="{{ $rec->name }}"
                                        onerror="this.onerror=null; this.src='https://placehold.co/200x200/0f172a/f59e0b?text=No+Pic';"
                                        class="w-full h-full object-contain p-2">
                                    @else
                                    <i class="fa-solid fa-box-open text-4xl text-slate-700 group-hover:scale-110 group-hover:text-amber-500/20 transition-all duration-500"></i>
                                    @endif

                                    @if($recIsOutofStock)
                                    <div class="absolute inset-0 bg-black/70 backdrop-blur-[2px] flex items-center justify-center z-20">
                                        <span class="bg-rose-600 text-white font-black text-[10px] px-3 py-1 rounded-full shadow-xl">Habis</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="p-4 flex flex-col flex-grow">
                                    <p class="text-[10px] text-amber-500 font-bold uppercase tracking-wider mb-1">{{ $rec->brand }}</p>
                                    <h3 class="font-medium text-xs text-slate-300 leading-snug line-clamp-2 mb-2 group-hover:text-white">{{ $rec->name }}</h3>
                                    <p class="font-black text-sm text-white mt-auto">Rp {{ number_format($recPrice->price ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </a>

                            @if(!$recIsOutofStock)
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0 z-30">
                                @auth
                                <form action="{{ route('cart.add', $rec->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="Tambah ke Keranjang"
                                        class="w-10 h-10 bg-amber-500 text-slate-900 font-black rounded-full flex items-center justify-center hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.5)] hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-cart-plus text-sm"></i>
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('login') }}"
                                    class="w-10 h-10 bg-amber-500 text-slate-900 font-black rounded-full flex items-center justify-center hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.5)] hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-cart-plus text-sm"></i>
                                </a>
                                @endauth
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        const pricePerItem = {{ $retailPrice->price ?? 0 }};
        const qtyInput = document.getElementById('qtyInput');
        const qtyDisplay = document.getElementById('qtyDisplay');
        const subtotalText = document.getElementById('subtotalText');

        function updateSubtotal() {
            let currentQty = parseInt(qtyInput.value);
            let total = currentQty * pricePerItem;
            subtotalText.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function increaseQty(maxStock) {
            if (parseInt(qtyInput.value) < maxStock) {
                let newVal = parseInt(qtyInput.value) + 1;
                qtyInput.value = newVal;
                qtyDisplay.innerText = newVal;
                updateSubtotal();
            }
        }

        function decreaseQty() {
            if (parseInt(qtyInput.value) > 1) {
                let newVal = parseInt(qtyInput.value) - 1;
                qtyInput.value = newVal;
                qtyDisplay.innerText = newVal;
                updateSubtotal();
            }
        }

        // ==========================================
        // LOGIKA MIDTRANS - TOMBOL BELI LANGSUNG
        // ==========================================
        const btnBeliLangsung = document.getElementById('btn-beli-langsung');
        if (btnBeliLangsung) {
            btnBeliLangsung.addEventListener('click', async function() {
                const productId = this.getAttribute('data-product-id');
                const qty = document.getElementById('qtyInput').value;

                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
                this.disabled = true;

                try {
                    const response = await fetch("{{ route('customer.payment.initiate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            qty: qty
                        })
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        window.snap.pay(data.snap_token, {
                            onSuccess: async function(result) {
                                await fetch("{{ route('customer.payment.update-status') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id,
                                        transaction_status: result.transaction_status
                                    })
                                });

                                alert("Pembayaran Berhasil! Pesanan sedang diproses.");
                                window.location.href = "/customer/transactions";
                            },
                            onPending: function(result) {
                                alert("Silakan selesaikan pembayaran!");
                            },
                            onError: function(result) {
                                alert("Pembayaran Gagal!");
                            },
                            onClose: function() {
                                alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                            }
                        });
                    } else {
                        alert(data.message || 'Gagal memproses token.');
                    }
                } catch (error) {
                    console.error(error);
                    alert("Koneksi ke server bermasalah.");
                } finally {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            });
        }
    </script>
</body>

</html>