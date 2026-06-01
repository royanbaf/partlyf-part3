<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist Saya | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 🎨 TEMA PUTIH SINKRON SEMPURNA DENGAN SIDEBAR CUSTOMER PARTLYFE */
        body { background-color: #f8fafc; color: #334155; overflow-x: hidden; }
        .glass-header { 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(226, 232, 240, 0.8); 
        }
        .white-card { 
            background: #ffffff; 
            border: 1px solid rgba(226, 232, 240, 0.9); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; } .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex selection:bg-amber-100 selection:text-amber-900">

    {{-- Sidebar Customer (Putih Bersih) --}}
    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">
        {{-- Efek Ornamen Soft Glow Latar Belakang --}}
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-amber-500/5 rounded-full filter blur-[120px] pointer-events-none z-0"></div>

        {{-- Header Atas Cerah --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-3 tracking-tight">
                <i class="fa-solid fa-heart text-rose-500"></i> Wishlist Tersimpan
            </h2>
            <div class="flex items-center gap-6">
                <a href="{{ route('customer.dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-amber-600 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Katalog
                </a>
            </div>
        </header>

        {{-- Area Konten Utama --}}
        <main class="flex-1 overflow-y-auto p-8 relative z-10 max-w-[1200px] mx-auto w-full">
            
            @if($wishlists->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    @foreach($wishlists as $wishlist)
                    @php 
                        $product = $wishlist->product;
                        $isOutofStock = $product->current_stock <= 0; 
                        $retailPrice = $product->prices->where('price_level', 1)->first(); 
                        
                        // Tarik rute berkas foto suku cadang langsung dari database
                        $customerPathFoto = DB::table('product_images')->where('product_id', $product->id)->value('image_path');
                    @endphp

                    <div class="white-card rounded-3xl hover:shadow-[0_12px_24px_-10px_rgba(0,0,0,0.08)] hover:border-amber-300 transition-all duration-300 group flex flex-col h-full overflow-hidden {{ $isOutofStock ? 'opacity-60' : '' }} relative">
                        
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="absolute top-3 right-3 z-20">
                            @csrf
                            <button type="submit" class="w-8 h-8 bg-white/90 backdrop-blur-sm text-slate-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-white transition shadow-sm border border-slate-100">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>

                        <a href="{{ route('product.detail', $product->id) }}" class="flex-grow flex flex-col cursor-pointer">
                            {{-- Kontainer Gambar Suku Cadang --}}
                            <div class="h-44 bg-slate-50/80 flex items-center justify-center relative border-b border-slate-100 overflow-hidden">
                                @if($customerPathFoto)
                                    <img src="{{ asset('storage/' . $customerPathFoto) }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 p-2">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-50">
                                        <i class="fa-solid fa-box-open text-4xl text-slate-300 group-hover:scale-105 transition-all duration-500"></i>
                                    </div>
                                @endif

                                @if($isOutofStock)
                                    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-20">
                                        <span class="bg-slate-800 text-white font-black text-[10px] px-3 py-1 rounded-full uppercase tracking-widest shadow-md">Stok Habis</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Keterangan Identitas Produk --}}
                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="font-bold text-sm text-slate-800 leading-snug line-clamp-2 mb-1 group-hover:text-amber-600 transition">{{ $product->name }}</h3>
                                <p class="font-black text-base text-amber-600 mt-auto pt-3">Rp {{ number_format($retailPrice->price ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </a>

                        @if(!$isOutofStock)
                        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold py-2.5 rounded-xl transition flex justify-center items-center gap-2 text-xs shadow-sm shadow-amber-500/10">
                                    <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                {{-- Tampilan Keadaan Kosong (Empty State) Cerah --}}
                <div class="flex flex-col items-center justify-center h-[60vh] text-center">
                    <div class="w-20 h-20 bg-white rounded-3xl border border-slate-100 flex items-center justify-center shadow-sm mb-6 text-slate-300">
                        <i class="fa-solid fa-heart-circle-xmark text-4xl text-slate-300"></i>
                    </div>
                    <h2 class="text-xl font-black text-slate-800 mb-1">Belum Ada Barang Incaran</h2>
                    <p class="text-sm font-medium text-slate-400 mb-6">Pilih suku cadang favoritmu dan amankan di sini untuk dipesan nanti.</p>
                    <a href="{{ route('customer.dashboard') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-sm">Lihat Katalog</a>
                </div>
            @endif

        </main>
    </div>
</body>
</html>