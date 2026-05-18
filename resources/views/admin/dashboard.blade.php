<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #020617; color: white; }
        .glass-card { background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .table-header { background: rgba(15, 23, 42, 0.9); }
    </style>
</head>
<body class="font-sans flex h-screen overflow-hidden text-slate-200">

    @include('layouts.admin-sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full filter blur-[120px] pointer-events-none"></div>

        <header class="h-20 border-b border-white/5 flex items-center justify-between px-10 flex-shrink-0 z-50">
            <div>
                <h2 class="text-xl font-bold text-white">Dashboard Overview</h2>
                <p class="text-xs text-slate-500">Pantau performa bisnis Partlyfe hari ini.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right mr-4 hidden md:block">
                    <p class="text-sm font-bold text-white">{{ Auth::user()->name ?? 'Admin Master' }}</p>
                    <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest">Administrator</p>
                </div>
                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center font-black text-slate-900 border-2 border-indigo-400/50">
                    A
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-10 scrollbar-hide relative z-10">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-money-bill-trend-up text-5xl text-indigo-400"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Pendapatan</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">Rp 128.450.000</h3>
                    <p class="text-[10px] text-emerald-400 mt-2 font-bold"><i class="fa-solid fa-arrow-up"></i> +12% dari bulan lalu</p>
                </div>

                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group border-l-4 border-l-amber-500">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Pesanan Baru</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">42 Pesanan</h3>
                    <p class="text-[10px] text-amber-400 mt-2 font-bold">Perlu diproses segera</p>
                </div>

                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Produk</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">1.240 <span class="text-sm font-normal text-slate-500 italic">SKU</span></h3>
                    <p class="text-[10px] text-slate-500 mt-2 font-bold">12 Stok Habis</p>
                </div>

                <div class="glass-card rounded-3xl p-6 relative overflow-hidden group border-l-4 border-l-indigo-500">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Pelanggan Aktif</p>
                    <h3 class="text-2xl font-black text-white tracking-tight">856 User</h3>
                    <p class="text-[10px] text-indigo-400 mt-2 font-bold">24 user baru minggu ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 glass-card rounded-3xl overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
                        <h3 class="font-black text-white uppercase tracking-wider text-sm">Transaksi Terbaru</h3>
                        <button class="text-xs font-bold text-indigo-400 hover:underline">Lihat Semua</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="table-header text-[10px] uppercase tracking-widest text-slate-500">
                                    <th class="px-6 py-4 font-bold">Invoice</th>
                                    <th class="px-6 py-4 font-bold">Pelanggan</th>
                                    <th class="px-6 py-4 font-bold">Status</th>
                                    <th class="px-6 py-4 font-bold text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm">
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 font-mono text-indigo-400 font-bold">#PTF-8821</td>
                                    <td class="px-6 py-4 text-white font-medium">Agus Surya</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-amber-500/10 text-amber-500 text-[10px] font-black px-2 py-1 rounded-md border border-amber-500/20">DIPROSES</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-white">Rp 850.000</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 font-mono text-indigo-400 font-bold">#PTF-8820</td>
                                    <td class="px-6 py-4 text-white font-medium">Steven Anthony</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-emerald-500/10 text-emerald-500 text-[10px] font-black px-2 py-1 rounded-md border border-emerald-500/20">SELESAI</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-white">Rp 2.150.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-6">
                    <h3 class="font-black text-white uppercase tracking-wider text-sm mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Stok Menipis
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white/5 border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center border border-white/10 text-slate-600">
                                    <i class="fa-solid fa-oil-can"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white line-clamp-1">Oli Mesin Motul 1L</p>
                                    <p class="text-[10px] text-slate-500 italic">SKU: MOT-001</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-rose-500">Sisa 2</p>
                                <button class="text-[9px] font-bold text-indigo-400 uppercase mt-1">Restock</button>
                            </div>
                        </div>
                        </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>


<?php


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Memanggil file dashboard.blade.php di folder resources/views/admin/
        return view('admin.dashboard');
    }
}