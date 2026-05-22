<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-b: 1px solid rgba(226, 232, 240, 0.8); 
        }
        .luxury-card-flat {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(148, 163, 184, 0.04);
        }
        .qty-btn-light {
            width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 1rem; font-weight: 700; transition: all 0.15s; cursor: pointer;
        }
        .qty-btn-light:hover { background: rgba(0,0,0,0.05); }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

        {{-- Header --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50">
            <div class="text-sm font-bold text-slate-400">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-amber-600 transition-colors">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-40"></i>
                <span class="text-slate-700">Keranjang Belanja</span>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('customer.profile') }}" class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-500 text-slate-900 rounded-full flex items-center justify-center font-black text-sm" style="box-shadow: 0 4px 12px rgba(245,158,11,0.2);">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </a>
            </div>
        </header>

        {{-- Main Cart Area --}}
        <main class="flex-1 overflow-y-auto p-8 bg-[#f8fafc]">
            <div class="max-w-[1200px] mx-auto">
                <h1 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-cart-shopping text-amber-500"></i> Keranjang Anda
                </h1>

                @if(isset($cartItems) && $cartItems->count() > 0)
                <div class="flex gap-8 items-start">
                    
                    {{-- LIST ITEM KERANJANG --}}
                    <div class="flex-grow space-y-4">
                        @php $totalCartPrice = 0; @endphp
                        @foreach($cartItems as $item)
                        @php 
                            $prodPrice = $item->product->prices->where('price_level', 1)->first()->price ?? 0;
                            $itemSubtotal = $prodPrice * $item->qty;
                            $totalCartPrice += $itemSubtotal;
                        @endphp
                        <div class="luxury-card-flat rounded-2xl p-5 bg-white flex items-center justify-between gap-6" id="cart-item-{{ $item->id }}">
                            {{-- Foto Produk --}}
                            <div class="w-20 h-20 bg-slate-50 rounded-xl border border-slate-100 flex-shrink-0 p-2 flex items-center justify-center">
                                @if($item->product->images && $item->product->images->isNotEmpty())
                                    <img src="{{ asset('storage/products/' . basename($item->product->images->first()->image_path)) }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <i class="fa-solid fa-box text-slate-300 text-xl"></i>
                                @endif
                            </div>

                            {{-- Informasi Nama & Merk --}}
                            <div class="flex-grow min-w-0">
                                <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wider mb-0.5">{{ $item->product->brand }}</p>
                                <a href="{{ route('product.detail', $item->product_id) }}" class="text-sm font-bold text-slate-800 hover:text-amber-600 transition-colors line-clamp-1 mb-1">
                                    {{ $item->product->name }}
                                </a>
                                <p class="text-xs font-black text-slate-900">Rp {{ number_format($prodPrice, 0, ',', '.') }}</p>
                            </div>

                            {{-- Kontrol Kuantitas Jumlah --}}
                            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1 flex-shrink-0">
                                <button type="button" class="qty-btn-light text-slate-500" onclick="updateCartQty({{ $item->id }}, -1)">−</button>
                                <span id="display-qty-{{ $item->id }}" class="w-8 text-center font-black text-slate-800 text-sm select-none">{{ $item->qty }}</span>
                                <button type="button" class="qty-btn-light text-amber-600" onclick="updateCartQty({{ $item->id }}, 1)">+</button>
                            </div>

                            {{-- Subtotal Item & Tombol Hapus --}}
                            <div class="text-right flex-shrink-0 min-w-[120px]">
                                <p class="text-sm font-black text-amber-700 mb-1" id="item-subtotal-{{ $item->id }}">
                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                </p>
                                <button type="button" onclick="deleteCartItem({{ $item->id }})" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- RINGKASAN BELANJA / CHECKOUT BOX --}}
                    <div class="w-[360px] flex-shrink-0 sticky top-4">
                        <div class="luxury-card-flat rounded-3xl p-6 bg-white shadow-sm">
                            <h3 class="font-black text-slate-800 mb-5 text-base pb-3 border-b border-slate-100">Ringkasan Belanja</h3>
                            
                            <div class="flex justify-between items-center mb-6">
                                <p class="text-sm text-slate-500 font-medium">Total Harga Barang</p>
                                <p class="text-lg font-black text-slate-900" id="cart-total-text">Rp {{ number_format($totalCartPrice, 0, ',', '.') }}</p>
                            </div>

                            <button type="button" id="btn-checkout-cart" class="w-full bg-gradient-to-r from-amber-400 to-amber-500 text-slate-900 font-black py-3.5 rounded-xl text-sm flex items-center justify-center gap-2 hover:brightness-105 transition-all shadow-md shadow-amber-500/10">
                                <i class="fa-solid fa-credit-card"></i> Lanjut ke Pembayaran
                            </button>
                        </div>
                    </div>

                </div>
                @else
                <div class="py-20 text-center rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <i class="fa-solid fa-basket-shopping text-5xl text-slate-200 mb-4"></i>
                    <p class="text-base text-slate-500 font-bold">Keranjang belanja Anda masih kosong</p>
                    <a href="{{ route('customer.dashboard') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-slate-900 font-black text-xs uppercase tracking-wider rounded-xl hover:bg-amber-600 transition-colors">
                        Kembali Belanja Suku Cadang
                    </a>
                </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        // Logika update kuantitas Ajax / Frontend
        function updateCartQty(itemId, change) {
            const qtyDisplay = document.getElementById('display-qty-' + itemId);
            let currentQty = parseInt(qtyDisplay.innerText);
            let newQty = currentQty + change;
            if (newQty < 1) return;

            qtyDisplay.innerText = newQty;
            // Di sini kamu bisa menyuntikkan fungsi Fetch/Axios API untuk meng-update record qty di database kamu!
        }

        function deleteCartItem(itemId) {
            if(confirm('Hapus item ini dari keranjang?')) {
                document.getElementById('cart-item-' + itemId).remove();
                // Di sini masukkan request API delete item keranjang kamu
            }
        }

       // ==========================================
        // MENGARAHKAN KE HALAMAN RINGKASAN CHECKOUT
        // ==========================================
        const btnCheckoutCart = document.getElementById('btn-checkout-cart');
        if (btnCheckoutCart) {
            btnCheckoutCart.addEventListener('click', function() {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memuat Ringkasan...';
                this.disabled = true;
                
                // Lempar ke halaman Ringkasan Belanja
                window.location.href = "{{ route('customer.checkout') }}";
            });
        }
    </script>
</body>
</html>