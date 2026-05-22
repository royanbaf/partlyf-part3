<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ringkasan Belanja | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        .luxury-card-flat { background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 4px 20px rgba(148, 163, 184, 0.04); }
        .glass-header { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-b: 1px solid rgba(226, 232, 240, 0.8); }
        
        /* Checkbox Partlyfe Style */
        .custom-checkbox { 
            appearance: none; width: 1.25rem; height: 1.25rem; border: 2px solid #cbd5e1; 
            border-radius: 0.375rem; outline: none; cursor: pointer; transition: all 0.2s; position: relative; 
        }
        .custom-checkbox:checked { background-color: #f59e0b; border-color: #f59e0b; }
        .custom-checkbox:checked::after { 
            content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900; 
            color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.75rem; 
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex selection:bg-amber-100 selection:text-amber-900">
    @include('layouts.sidebar')
    
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header class="h-20 glass-header flex items-center px-8 z-50 sticky top-0">
            <h1 class="text-xl font-black text-slate-800 tracking-tight">
                <i class="fa-solid fa-lock text-amber-500 mr-2"></i> Checkout Partlyfe
            </h1>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-[1100px] mx-auto flex gap-8 items-start">
                
                {{-- BAGIAN KIRI: ALAMAT & BARANG --}}
                <div class="flex-grow space-y-6">
                    
                    {{-- 1. KOTAK ALAMAT PENGIRIMAN --}}
                    <div class="luxury-card-flat rounded-3xl p-7">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-black text-slate-800 text-base"><i class="fa-solid fa-location-dot text-amber-500 mr-2"></i> Alamat Pengiriman</h3>
                            <button type="button" onclick="toggleAddressForm()" class="text-xs font-bold text-amber-600 hover:text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 transition-colors">
                                Ubah Alamat
                            </button>
                        </div>
                        
                        {{-- Tampilan Alamat Aktif --}}
                        <div id="address-display" class="flex items-start gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div>
                                <p class="font-black text-slate-800 text-sm">{{ Auth::user()->name }} <span class="text-slate-400 font-normal">({{ Auth::user()->phone ?? 'Belum ada nomor HP' }})</span></p>
                                <p id="current-address-text" class="text-sm text-slate-500 mt-1 leading-relaxed">{{ Auth::user()->address ?? 'Alamat belum diatur. Silakan perbarui alamat Anda untuk melanjutkan.' }}</p>
                            </div>
                        </div>

                        {{-- Form Ubah Alamat (Tersembunyi secara default) --}}
                        <div id="address-form" class="hidden mt-4 pt-4 border-t border-slate-100">
                            <textarea id="new-address-input" rows="3" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all mb-3" placeholder="Ketik alamat lengkap pengiriman baru...">{{ Auth::user()->address }}</textarea>
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="toggleAddressForm()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
                                <button type="button" onclick="saveAddress(event)" class="px-4 py-2 rounded-xl text-xs font-black bg-amber-500 text-slate-900 hover:bg-amber-600 shadow-sm shadow-amber-500/20 transition-all">Simpan Alamat</button>
                            </div>
                        </div>
                    </div>

                    {{-- 2. KOTAK RINCIAN BARANG --}}
                    <div class="luxury-card-flat rounded-3xl p-7">
                        <h3 class="font-black text-slate-800 mb-5 text-base pb-3 border-b border-slate-100">
                            <i class="fa-solid fa-store text-amber-500 mr-2"></i> Partlyfe
                        </h3>
                        
                        <div class="space-y-5">
                            @foreach($checkoutItems as $item)
                            <div class="flex gap-5 items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center p-2 flex-shrink-0">
                                    <img src="{{ $item->image ?? 'https://placehold.co/100x100' }}" class="max-w-full max-h-full object-contain">
                                </div>
                                <div class="flex-grow">
                                    <p class="text-[10px] text-amber-600 font-black uppercase tracking-widest mb-1">{{ $item->brand }}</p>
                                    <p class="font-bold text-slate-800 text-sm line-clamp-1 mb-1">{{ $item->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $item->qty }} barang x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-black text-slate-900 text-base">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        {{-- Dropdown Pilihan Ekspedisi --}}
                        <div class="mt-8 pt-5 border-t border-slate-100">
                            <h3 class="font-black text-slate-800 mb-3 text-sm">Pilih Ekspedisi Pengiriman</h3>
                            <select id="shipping-select" onchange="calculateTotal()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium text-slate-700 outline-none focus:border-amber-500 focus:bg-white transition-all cursor-pointer">
                                <option value="26000">Ekspedisi Standard (Estimasi 2-3 Hari) - Rp 26.000</option>
                                <option value="45000">Ekspedisi Express (Estimasi 1 Hari) - Rp 45.000</option>
                                <option value="85000">Kargo Spesial Suku Cadang Berat - Rp 85.000</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN KANAN: RINGKASAN BIAYA --}}
                <div class="w-[400px] flex-shrink-0 sticky top-4">
                    <div class="luxury-card-flat rounded-3xl p-7">
                        <h3 class="font-black text-slate-800 mb-6 text-base pb-3 border-b border-slate-100">Ringkasan Belanja</h3>
                        
                        {{-- Opsi Checkbox --}}
                        <div class="space-y-4 mb-6 pb-6 border-b border-slate-100">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" id="check-asuransi" onchange="calculateTotal()" class="custom-checkbox mt-0.5" checked>
                                <div>
                                    <p class="text-sm font-bold text-slate-700 group-hover:text-amber-600 transition-colors">Asuransi Pengiriman</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Melindungi pesanan hilang/rusak (Rp {{ number_format($asuransi, 0, ',', '.') }})</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" id="check-proteksi" onchange="calculateTotal()" class="custom-checkbox mt-0.5" checked>
                                <div>
                                    <p class="text-sm font-bold text-slate-700 group-hover:text-amber-600 transition-colors">Proteksi Produk Tambahan</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Garansi cacat pabrik 6 bulan (Rp {{ number_format($biayaProteksi, 0, ',', '.') }})</p>
                                </div>
                            </label>
                        </div>

                        {{-- Rincian Angka Dinamis --}}
                        <div class="space-y-3 text-sm text-slate-500 mb-6">
                            <div class="flex justify-between items-center"><p>Total Harga ({{ count($checkoutItems) }} Barang)</p><p class="font-bold text-slate-700">Rp {{ number_format($subtotal, 0, ',', '.') }}</p></div>
                            <div class="flex justify-between items-center"><p>Total Ongkos Kirim</p><p class="font-bold text-slate-700" id="txt-ongkir">Rp 26.000</p></div>
                            <div class="flex justify-between items-center" id="row-asuransi"><p>Total Asuransi</p><p class="font-bold text-slate-700">Rp {{ number_format($asuransi, 0, ',', '.') }}</p></div>
                            <div class="flex justify-between items-center" id="row-proteksi"><p>Total Biaya Proteksi</p><p class="font-bold text-slate-700">Rp {{ number_format($biayaProteksi, 0, ',', '.') }}</p></div>
                            <div class="flex justify-between items-center"><p>Biaya Layanan & Aplikasi</p><p class="font-bold text-slate-700">Rp {{ number_format($biayaLayanan, 0, ',', '.') }}</p></div>
                        </div>

                        <div class="flex justify-between items-center mb-6 pt-5 border-t border-slate-100">
                            <p class="text-sm font-black text-slate-800">Total Tagihan</p>
                            <p class="text-2xl font-black text-amber-600" id="txt-total-tagihan">Rp 0</p>
                        </div>

                        <button type="button" id="btn-bayar-sekarang" class="w-full bg-gradient-to-r from-amber-400 to-amber-500 text-slate-900 font-black py-4 rounded-xl text-sm flex items-center justify-center gap-2 hover:brightness-105 transition-all shadow-lg shadow-amber-500/20">
                            <i class="fa-solid fa-shield-halved"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Nilai dasar (dikirim dari Controller)
        const baseSubtotal = {{ $subtotal }};
        const feeAsuransi = {{ $asuransi }};
        const feeProteksi = {{ $biayaProteksi }};
        const feeLayanan = {{ $biayaLayanan }};

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Kalkulasi Total secara Real-Time berdasarkan Opsi Checkbox & Select
        function calculateTotal() {
            const selectOngkir = document.getElementById('shipping-select');
            const ongkir = parseInt(selectOngkir.value) || 0;
            
            const isAsuransi = document.getElementById('check-asuransi').checked;
            const isProteksi = document.getElementById('check-proteksi').checked;

            const totalAsuransi = isAsuransi ? feeAsuransi : 0;
            const totalProteksi = isProteksi ? feeProteksi : 0;

            // Atur tampilan baris di ringkasan jika tidak dicentang
            document.getElementById('row-asuransi').style.display = isAsuransi ? 'flex' : 'none';
            document.getElementById('row-proteksi').style.display = isProteksi ? 'flex' : 'none';
            document.getElementById('txt-ongkir').innerText = formatRupiah(ongkir);

            // Hitung Grand Total & Tampilkan
            const grandTotal = baseSubtotal + ongkir + totalAsuransi + totalProteksi + feeLayanan;
            document.getElementById('txt-total-tagihan').innerText = formatRupiah(grandTotal);
        }

        // Membuka/Menutup Form Edit Alamat
        function toggleAddressForm() {
            const display = document.getElementById('address-display');
            const form = document.getElementById('address-form');
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                display.classList.add('hidden');
            } else {
                form.classList.add('hidden');
                display.classList.remove('hidden');
            }
        }

        // Simpan Alamat langsung di halaman (AJAX)
        async function saveAddress(event) {
            const btn = event.target;
            const newAddress = document.getElementById('new-address-input').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
            btn.disabled = true;
            
            try {
                // Endpoint Profile Update bawaan Laravel Breeze/Custom
                await fetch("{{ route('profile.update.address') }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ address: newAddress })
                });
                
                document.getElementById('current-address-text').innerText = newAddress;
                toggleAddressForm();
            } catch (error) {
                alert('Gagal memperbarui alamat. Pastikan route API tersedia.');
            } finally {
                btn.innerHTML = 'Simpan Alamat';
                btn.disabled = false;
            }
        }

        // Inisialisasi perhitungan saat load pertama kali
        calculateTotal();

        // Tombol Pemrosesan Pembayaran
        const btnBayar = document.getElementById('btn-bayar-sekarang');
        btnBayar.addEventListener('click', async function() {
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghubungkan Midtrans...';
            this.disabled = true;

            try {
                const res = await fetch("{{ route('customer.payment.initiate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        product_id: "{{ $productId ?? '' }}", 
                        qty: "{{ $qty ?? 1 }}"
                        // Opsional: Kamu bisa melempar data fee tambahan ke backend di sini jika perlu
                    })
                });
                
                const data = await res.json();
                if (data.status === 'success') {
                    window.snap.pay(data.snap_token, {
                        onSuccess: async (result) => {
                            await fetch("{{ route('customer.payment.update-status') }}", {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                                body: JSON.stringify({ order_id: result.order_id, transaction_status: result.transaction_status })
                            });
                            window.location.href = "/customer/transactions";
                        },
                        onPending: () => { window.location.href = "/customer/transactions"; },
                        onError: () => { window.location.href = "/customer/transactions"; },
                        onClose: () => { window.location.href = "/customer/transactions"; }
                    });
                } else {
                    alert(data.message || 'Gagal menyiapkan data tagihan.');
                }
            } catch (e) {
                console.error(e);
                alert("Koneksi ke sistem pembayaran bermasalah.");
            } finally {
                this.innerHTML = originalHTML;
                this.disabled = false;
            }
        });
    </script>
</body>
</html>