<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Nota {{ $customer->name }} | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #020617; color: white; }
        .glass-card { background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .table-header { background: rgba(15, 23, 42, 0.9); }
    </style>
</head>
<body class="font-sans flex h-screen overflow-hidden text-slate-200">

    {{-- Sidebar Admin --}}
    @include('layouts.admin-sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full filter blur-[120px] pointer-events-none"></div>

        <header class="h-20 border-b border-white/5 flex items-center justify-between px-10 flex-shrink-0 z-50">
            <div>
                <h2 class="text-xl font-bold text-white">Riwayat Transaksi Pelanggan</h2>
                <p class="text-xs text-slate-500">Memantau berkas nota atas nama: <span class="text-indigo-400 font-bold">{{ $customer->name }}</span></p>
            </div>
            <a href="{{ url('/admin/customers') }}" class="text-xs font-bold bg-white/5 border border-white/10 px-4 py-2 rounded-xl hover:bg-white/10 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </header>

        <main class="flex-1 overflow-y-auto p-10 relative z-10">
            <div class="glass-card rounded-3xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="table-header text-[10px] uppercase tracking-widest text-slate-500 border-b border-white/5">
                                <th class="px-6 py-4 font-bold">Tanggal</th>
                                <th class="px-6 py-4 font-bold">Nomor Invoice</th>
                                <th class="px-6 py-4 font-bold">Status Pesanan</th>
                                <th class="px-6 py-4 font-bold text-right">Total Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            @forelse($customerTransactions ?? [] as $t)
                                @php
                                    $statusRow = strtolower(trim($t->status ?? 'pending'));
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        {{ date('d M Y, H:i', strtotime($t->created_at)) }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-indigo-400 font-bold">
                                        {{ $t->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(in_array($statusRow, ['pending', 'unpaid', 'menunggu pembayaran']))
                                            <span class="bg-amber-500/10 text-amber-500 text-[10px] font-black px-2.5 py-1 rounded-md border border-amber-500/20 uppercase tracking-wide">MENUNGGU</span>
                                        @elseif(in_array($statusRow, ['processing', 'diproses', 'sedang diproses']))
                                            <span class="bg-blue-500/10 text-blue-400 text-[10px] font-black px-2.5 py-1 rounded-md border border-blue-500/20 uppercase tracking-wide">DIPROSES</span>
                                        @elseif(in_array($statusRow, ['success', 'selesai', 'settlement', 'paid']))
                                            <span class="bg-emerald-500/10 text-emerald-400 text-[10px] font-black px-2.5 py-1 rounded-md border border-emerald-500/20 uppercase tracking-wide">SELESAI</span>
                                        @else
                                            <span class="bg-rose-500/10 text-rose-400 text-[10px] font-black px-2.5 py-1 rounded-md border border-rose-500/20 uppercase tracking-wide">BATAL</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-white font-mono">
                                        Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-xs text-slate-500 italic">Pelanggan ini belum memiliki riwayat transaksi masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>