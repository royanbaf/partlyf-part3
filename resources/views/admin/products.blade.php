<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Produk | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #020617; color: white; }
        .glass-card { background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .table-header { background: rgba(15, 23, 42, 0.9); }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-sans flex h-screen overflow-hidden text-slate-200">

    {{-- Sidebar Layout Component --}}
    @include('layouts.admin-sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full filter blur-[120px] pointer-events-none"></div>

        {{-- Header Atas Konten --}}
        <header class="h-20 border-b border-white/5 flex items-center justify-between px-10 flex-shrink-0 z-40">
            <div>
                <h2 class="text-xl font-bold text-white">Produk & Inventori</h2>
                <p class="text-xs text-slate-500">Manajemen stok suku cadang Partlyfe Core.</p>
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

        {{-- Main Container Content --}}
        <main class="flex-1 overflow-y-auto p-10 relative z-10 scrollbar-hide">
            
            {{-- Bagian Atas Judul & Tombol Tambah --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-wider">Daftar Suku Cadang</h3>
                    <p class="text-xs text-slate-500">Total {{ count($products ?? []) }} SKU terdaftar aktif di database Partlyfe.</p>
                </div>
                
                {{-- Search Bar --}}
                <form method="GET" class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk, merek, atau SKU..."
                        class="w-64 bg-slate-900/60 border border-white/10 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                </form>
                
                <button onclick="toggleModal('addProductModal')" 
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-wider px-6 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </button>
            </div>

            {{-- Flash Session Message Alert --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-base"></i> 
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-base"></i> 
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Table Rendering Element --}}
            <div class="glass-card rounded-3xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="table-header text-[10px] uppercase tracking-widest text-slate-500 border-b border-white/5">
                                <th class="px-6 py-5 font-bold">Visual</th>
                                <th class="px-6 py-5 font-bold">Informasi Suku Cadang</th>
                                <th class="px-6 py-5 font-bold">Stok Gudang</th>
                                <th class="px-6 py-5 font-bold">Harga Retail Level 1</th>
                                <th class="px-6 py-5 font-bold text-center">Aksi CRUD</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            @forelse($products ?? [] as $p)
                                @php
                                    $hargaRetail = DB::table('product_prices')->where('product_id', $p->id)->where('price_level', 1)->value('price') ?? 0;
                                    $pathFoto = DB::table('product_images')->where('product_id', $p->id)->value('image_path');
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    {{-- Kolom Gambar Produk --}}
                                    <td class="px-6 py-5">
                                        @php
                                            $imagePath = $pathFoto ?? null;
                                        @endphp
                                        @if($imagePath)
                                            <img src="{{ asset('storage/' . $imagePath) }}" class="w-12 h-12 object-cover rounded-xl border border-white/10 shadow-md" onerror="this.parentElement.innerHTML='<div class=&quot;w-12 h-12 bg-slate-800 rounded-xl border border-white/5 flex items-center justify-center text-slate-600&quot;><i class=&quot;fa-solid fa-box text-xs&quot;></i></div>'">
                                        @else
                                            <div class="w-12 h-12 bg-slate-800 rounded-xl border border-white/5 flex items-center justify-center text-slate-600">
                                                <i class="fa-solid fa-box text-xs"></i>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    {{-- Kolom Info Nama & Brand --}}
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-bold text-white">{{ $p->name }}</p>
                                        <p class="text-[10px] text-slate-500 italic uppercase tracking-wider mt-0.5 font-mono">
                                            SKU: {{ $p->item_code }} | Merek: {{ $p->brand }}
                                        </p>
                                    </td>
                                    
                                    {{-- Kolom Indikator Stok Kritis --}}
                                    <td class="px-6 py-5">
                                        @if($p->current_stock <= 0)
                                            <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">Ludes</span>
                                        @elseif($p->current_stock <= 5)
                                            <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">Kritis ({{ $p->current_stock }})</span>
                                        @else
                                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide font-mono">{{ $p->current_stock }} Pcs</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Kolom Harga --}}
                                    <td class="px-6 py-5 font-mono font-black text-white">
                                        Rp {{ number_format($hargaRetail, 0, ',', '.') }}
                                    </td>
                                    
                                    {{-- Kolom Aksi Kontrol --}}
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            {{-- Tombol Edit Data Produk --}}
                                            <button type="button" 
                                                onclick="openEditModal('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ addslashes($p->brand) }}', '{{ addslashes($p->item_code) }}', '{{ $p->current_stock }}', '{{ $hargaRetail }}', '{{ $p->category_id }}', '{{ $p->min_stock ?? 0 }}')"
                                                class="text-indigo-400 hover:text-indigo-300 transition-colors text-base" title="Edit Data">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            {{-- Tombol Hapus Produk Permanen --}}
                                            <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ addslashes($p->name) }} secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-400 transition-colors text-base" title="Hapus Permanen">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-xs text-slate-500 italic font-mono">Belum ada produk terdaftar di database Partlyfe.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    @php
    $categories = $categories ?? DB::table('categories')->get();
@endphp

{{-- MODAL 1: FORM TAMBAH PRODUK --}}
    <div id="addProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="glass-card w-full max-w-lg rounded-3xl p-8 border border-white/10 shadow-2xl relative animate-fadeIn">
            <div class="mb-5">
                <h3 class="text-lg font-black text-white uppercase tracking-wider">Tambah Produk Baru</h3>
                <p class="text-xs text-slate-500">Masukkan spesifikasi suku cadang baru beserta unggahan berkas gambar.</p>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Foto Produk (.jpg/.png)</label>
                        <input type="file" name="image" accept="image/*" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-2 text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Nama Produk</label>
                        <input type="text" name="name" required placeholder="Oli Mesin Yamalube" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Brand / Merek</label>
                            <input type="text" name="brand" required placeholder="Yamalube" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Kode Item / SKU (Auto)</label>
                            <input type="text" name="item_code" placeholder="Otomatis terisi" readonly class="w-full bg-slate-800/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-300 font-mono cursor-not-allowed">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Kategori</label>
                            <select name="category_id" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Stok Awal</label>
                            <input type="number" name="current_stock" min="0" required placeholder="10" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Min Stok</label>
                            <input type="number" name="min_stock" min="0" value="0" placeholder="5" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Harga Retail (Rp)</label>
                            <input type="number" name="price" min="0" required placeholder="70000" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Lokasi Rak (Opsional)</label>
                        <input type="text" name="rack_location" placeholder="Rak A-2" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Deskripsi Produk</label>
                        <textarea name="description" rows="3" placeholder="Kecocokan: Yamaha NMAX 2018-2022, Honda Vario 125..." class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                    <button type="button" onclick="toggleModal('addProductModal')" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-wider px-6 py-3 rounded-xl transition">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: FORM EDIT/UPDATE PRODUK --}}
    <div id="editProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="glass-card w-full max-w-lg rounded-3xl p-8 border border-white/10 shadow-2xl relative animate-fadeIn">
            <div class="mb-5">
                <h3 class="text-lg font-black text-white uppercase tracking-wider">Edit Data Produk</h3>
                <p class="text-xs text-slate-500">Ubah spesifikasi stok gudang dan sesuaikan harga retail terbaru di sistem Partlyfe.</p>
            </div>
            <form id="editProductForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    @if(isset($product) && $product->image)
                        <div class="mb-2">
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Foto Lama</label>
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-20 h-20 object-cover rounded-xl border border-white/10">
                        </div>
                    @endif
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Ganti Foto (Opsional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-2 text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Nama Produk</label>
                        <input type="text" id="edit_name" name="name" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Brand / Merek</label>
                            <input type="text" id="edit_brand" name="brand" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Kode Item / SKU (Auto)</label>
                            <input type="text" id="edit_item_code" name="item_code" placeholder="Otomatis terisi" readonly class="w-full bg-slate-800/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-300 font-mono cursor-not-allowed">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Kategori</label>
                            <select id="edit_category_id" name="category_id" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ isset($product) && $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Stok Gudang</label>
                            <input type="number" id="edit_current_stock" name="current_stock" min="0" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Min Stok</label>
                            <input type="number" id="edit_min_stock" name="min_stock" min="0" value="0" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Harga Retail (Rp)</label>
                            <input type="number" id="edit_price" name="price" min="0" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Lokasi Rak</label>
                        <input type="text" id="edit_rack_location" name="rack_location" placeholder="Rak A-2" class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">Deskripsi Produk</label>
                        <textarea id="edit_description" name="description" rows="3" placeholder="Kecocokan: Yamaha NMAX 2018-2022..." class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end gap-3 border-t border-white/5 pt-5">
                    <button type="button" onclick="toggleModal('editProductModal')" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-wider px-6 py-3 rounded-xl transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

{{-- Driver JavaScript Kontrol Modal & Auto-Trigger Restock --}}
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.toggle('hidden');
        }

        function generateSKU() {
            const categorySelect = document.querySelector('#addProductModal select[name="category_id"]');
            const nameInput = document.querySelector('#addProductModal input[name="name"]');
            const skuInput = document.querySelector('#addProductModal input[name="item_code"]');
            
            const categoryName = categorySelect.options[categorySelect.selectedIndex]?.text || 'GEN';
            const productName = nameInput?.value || 'PRD';
            
            const catPrefix = categoryName.substring(0, 3).toUpperCase();
            const namePrefix = productName.replace(/\s+/g, '').substring(0, 3).toUpperCase();
            const year = new Date().getFullYear().toString().substring(2);
            
            const sku = `${catPrefix}-${namePrefix}-${year}`;
            if (skuInput) skuInput.value = sku;
        }

        function openEditModal(id, name, brand, item_code, current_stock, price, category_id, min_stock, rack_location = '', description = '') {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_brand').value = brand;
            document.getElementById('edit_item_code').value = item_code;
            document.getElementById('edit_current_stock').value = current_stock;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category_id').value = category_id;
            document.getElementById('edit_min_stock').value = min_stock;
            document.getElementById('edit_rack_location').value = rack_location;
            document.getElementById('edit_description').value = description;
            
            document.getElementById('editProductForm').action = `/admin/products/${id}`;
            
            const modal = document.getElementById('editProductModal');
            if (modal && modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const categorySelect = document.querySelector('#addProductModal select[name="category_id"]');
            const nameInput = document.querySelector('#addProductModal input[name="name"]');
            if (categorySelect) categorySelect.addEventListener('change', generateSKU);
            if (nameInput) nameInput.addEventListener('input', generateSKU);

            @if(isset($product))
                openEditModal(
                    '{{ $product->id }}', 
                    '{{ addslashes($product->name) }}', 
                    '{{ addslashes($product->brand) }}', 
                    '{{ addslashes($product->item_code) }}', 
                    '{{ $product->current_stock }}', 
                    '{{ $product->price }}',
                    '{{ $product->category_id }}',
                    '{{ $product->min_stock ?? 0 }}',
                    '{{ addslashes($product->rack_location ?? '') }}',
                    '{{ addslashes($product->description ?? '') }}'
                );
            @endif
        });
    </script>
</body>
</html>