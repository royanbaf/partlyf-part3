<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Nota | Partlyfe Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden font-sans" x-data="{ openModal: false, invoiceData: {} }">

    @include('layouts.admin-sidebar')

    <main class="flex-1 overflow-y-auto p-10 relative">
        
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-5">
                <a href="/admin/customers" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center hover:bg-white/10 transition-all border border-white/5">
                    <i class="fa-solid fa-chevron-left text-white"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">RIWAYAT <span class="text-indigo-400">NOTA</span></h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Manajemen Arsip Penjualan Pelanggan</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-900/40 rounded-[2rem] p-8 border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full filter blur-3xl"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center text-4xl font-black text-white mb-5 border-4 border-slate-400">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                        <h3 class="text-xl font-black text-white">{{ $customer->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">{{ $customer->email }}</p>
                        <div class="mt-4">
                            <span class="px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest">
                                {{ $customer->role === 'b2b' ? 'Mitra B2B' : 'Retail B2C' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-10 space-y-4 border-t border-white/5 pt-6 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Total Transaksi</span>
                            <span class="text-white font-black">{{ $transactions->count() }} Nota</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase text-[10px]">Total Spend</span>
                            <span class="text-emerald-400 font-black">Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="rounded-[2rem] overflow-hidden border border-white/5 bg-slate-900/40 shadow-2xl">
                    <div class="px-8 py-6 border-b border-white/5 bg-white/5">
                        <h3 class="font-black text-white text-sm uppercase tracking-widest flex items-center gap-3">
                            <i class="fa-solid fa-list-check text-indigo-400"></i> Log Aktivitas Pembelian (Klik Baris untuk Detail)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-950/50 text-[10px] uppercase tracking-widest text-slate-500">
                                    <th class="px-8 py-5 font-black">Nomor Invoice</th>
                                    <th class="px-8 py-5 font-black">Waktu Order</th>
                                    <th class="px-8 py-5 font-black text-center">Status</th>
                                    <th class="px-8 py-5 font-black text-right">Nominal</th>
                                    <th class="px-8 py-5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($transactions as $tx)
                                <tr class="group hover:bg-white/[0.03] transition-all cursor-pointer" 
                                    @click="openModal = true; 
                                            invoiceData = { 
                                                no: '{{ $tx->invoice_number }}', 
                                                date: '{{ $tx->created_at->format('d M Y, H:i') }}', 
                                                total: 'Rp {{ number_format($tx->total_amount, 0, ',', '.') }}',
                                                status: '{{ $tx->status }}',
                                                method: '{{ $tx->payment_method ?? 'Cash' }}'
                                            }">
                                    <td class="px-8 py-6">
                                        <p class="font-mono text-indigo-400 font-black text-sm">{{ $tx->invoice_number }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-slate-300 font-bold text-xs">{{ $tx->created_at->format('d F Y') }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $tx->created_at->format('H:i') }} WIB</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-3 py-1 rounded-md text-[9px] font-black uppercase border 
                                            {{ $tx->status == 'delivered' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-amber-500/10 text-amber-500 border-amber-500/20' }}">
                                            {{ $tx->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <p class="text-white font-black text-sm">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-all text-slate-500">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <i class="fa-solid fa-receipt text-5xl text-slate-800 mb-4 block"></i>
                                        <p class="text-slate-500 font-bold">Belum ada riwayat transaksi</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6" x-show="openModal" x-cloak>
            <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-md" @click="openModal = false"></div>
            <div class="relative bg-slate-900 border border-white/10 w-full max-w-md rounded-[2.5rem] overflow-hidden p-8" 
                 x-show="openModal" 
                 x-transition>
                <div class="text-center border-b border-dashed border-white/10 pb-6 mb-6">
                    <h4 class="text-2xl font-black text-white">PARTLYFE<span class="text-indigo-500">.</span></h4>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.3em] mt-1">E-Receipt Official</p>
                </div>
                <div class="space-y-4 mb-8 text-sm">
                    <div class="flex justify-between"><span class="text-[10px] font-bold text-slate-500 uppercase">No. Invoice</span><span class="text-xs font-black text-indigo-400 font-mono" x-text="invoiceData.no"></span></div>
                    <div class="flex justify-between"><span class="text-[10px] font-bold text-slate-500 uppercase">Waktu</span><span class="text-xs font-bold text-white" x-text="invoiceData.date"></span></div>
                    <div class="flex justify-between"><span class="text-[10px] font-bold text-slate-500 uppercase">Metode</span><span class="text-xs font-bold text-white" x-text="invoiceData.method"></span></div>
                </div>
                <div class="bg-white/5 rounded-2xl p-5 border border-white/5 mb-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-white">Item Produk</p>
                            <p class="text-[10px] text-slate-500 mt-1 italic">Kampas Rem Depan Vario (Zee Purchase)</p>
                        </div>
                        <span class="text-xs font-black text-indigo-400">2 Pcs</span>
                    </div>
                </div>
                <div class="flex justify-between items-center border-t border-dashed border-white/10 pt-6">
                    <span class="text-xs font-black text-white uppercase">Total Bayar</span>
                    <span class="text-2xl font-black text-emerald-400" x-text="invoiceData.total"></span>
                </div>
                <button @click="openModal = false" class="w-full mt-10 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-lg text-xs uppercase tracking-widest">Selesai & Tutup</button>
            </div>
        </div>
    </main>

</body>
</html>
<style>
    [x-cloak] { display: none !important; }
</style>