<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden">
    @include('layouts.admin-sidebar')
    
    <main class="flex-1 overflow-y-auto p-10">
        {{-- Header --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-white uppercase tracking-tight">Riwayat Transaksi</h2>
                <p class="text-xs text-slate-500">Pusat pemantauan dan audit pesanan masuk pelanggan B2C secara real-time.</p>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-slate-900/40 backdrop-blur-md rounded-3xl border border-white/5 overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-5">Tanggal Masuk</th>
                        <th class="px-6 py-5">Invoice</th>
                        <th class="px-6 py-5">Pelanggan</th>
                        <th class="px-6 py-5">Payment Method</th>
                        <th class="px-6 py-5">Status Order</th>
                        <th class="px-6 py-5 text-right">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-white/[0.02] transition-colors duration-150">
                            
                            <td class="px-6 py-5 text-xs text-slate-400 font-medium">
                                {{ isset($t->created_at) ? date('d M Y, H:i', strtotime($t->created_at)) : '-' }}
                            </td>

                            <td class="px-6 py-5 font-mono text-xs text-indigo-400 font-bold tracking-tight">
                                #{{ $t->invoice_number ?? $t->transaction_real_id }}
                            </td>

                            <td class="px-6 py-5 text-sm text-white font-semibold">
                                {{ $t->customer_name ?? 'Guest User' }}
                            </td>

                            <td class="px-6 py-5 text-xs text-slate-400 font-mono uppercase tracking-wide">
                                {{ $t->payment_method ?? 'MIDTRANS API' }}
                            </td>

                            {{-- BADGE TEKS SIMPEL & AMAN UNTUK MENTORING --}}
                            <td class="px-6 py-5">
                                @php
                                    $currentStatus = strtolower(trim($t->status ?? 'pending'));
                                @endphp
                                
                                @if(in_array($currentStatus, ['pending', 'unpaid', 'menunggu pembayaran']))
                                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider">
                                        Menunggu Bayar
                                    </span>
                                @elseif(in_array($currentStatus, ['processing', 'diproses', 'sedang diproses']))
                                    <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider">
                                        Sedang Diproses
                                    </span>
                                @elseif(in_array($currentStatus, ['success', 'selesai', 'settlement', 'paid']))
                                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider">
                                        Selesai
                                    </span>
                                @else
                                    <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-5 text-right font-mono font-black text-white text-sm">
                                Rp {{ number_format($t->total_amount ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-500 italic">
                                Belum ada berkas transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>