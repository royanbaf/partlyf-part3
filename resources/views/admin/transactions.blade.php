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
        <div class="mb-10">
            <h2 class="text-2xl font-black text-white uppercase tracking-tight">Riwayat Transaksi</h2>
            <p class="text-xs text-slate-500">Daftar pesanan dari pelanggan B2C.</p>
        </div>

        <div class="bg-slate-900/40 backdrop-blur-md rounded-3xl border border-white/5 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-widest text-slate-500">
                    <tr>
                        <th class="px-8 py-5">Invoice</th>
                        <th class="px-8 py-5">Pelanggan</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr class="hover:bg-white/[0.02]">
                        <td class="px-8 py-5 font-mono text-indigo-400 font-bold">#PTF-8821</td>
                        <td class="px-8 py-5 text-white">Agus Surya</td>
                        <td class="px-8 py-5">
                            <span class="bg-amber-500/10 text-amber-500 text-[10px] font-black px-3 py-1 rounded-md border border-amber-500/20">DIPROSES</span>
                        </td>
                        <td class="px-8 py-5 text-right font-black text-white">Rp 850.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>