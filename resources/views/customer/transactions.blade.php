<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Transaksi | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- 🚀 WAJIB: Script SDK Midtrans Snap Sandbox --}}
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        main::-webkit-scrollbar { width: 6px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes blobFloat {
            0%   { transform: translate(0px, 0px) scale(1); }
            33%  { transform: translate(30px, -50px) scale(1.02); }
            66%  { transform: translate(-20px, 20px) scale(0.98); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; will-change: transform; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(245,158,11,0.06) 0%, transparent 70%); top: -100px; left: -100px; animation: blobFloat 15s ease-in-out infinite; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(99,102,241,0.04) 0%, transparent 70%); bottom: -100px; right: -100px; animation: blobFloat 20s ease-in-out infinite reverse; }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-b: 1px solid rgba(226, 232, 240, 0.8); 
        }

        .customer-pagination-container nav > div:first-child { display: none; } 
        .customer-pagination-container nav > div:last-child { display: flex; justify-content: center; width: 100%; gap: 6px; }
        .customer-pagination-container .page-link, .customer-pagination-container span[aria-current="page"] > span, .customer-pagination-container nav span.relative {
            width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 12px !important; font-size: 13px; font-weight: 700; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(148, 163, 184, 0.05);
        }
        .customer-pagination-container a.page-link, .customer-pagination-container span.page-link { background-color: #ffffff !important; color: #64748b !important; border: 1px solid #e2e8f0 !important; }
        .customer-pagination-container a.page-link:hover { border-color: #f59e0b !important; color: #d97706 !important; background-color: #fffbeb !important; }
        .customer-pagination-container span[aria-current="page"] > span { background-color: #f59e0b !important; color: #ffffff !important; border: 1px solid #f59e0b !important; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2) !important; }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex selection:bg-amber-100 selection:text-amber-900">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">

        {{-- Header Halaman --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Riwayat Transaksi Saya</h1>
            <div class="flex items-center gap-6">
                <a href="{{ route('customer.dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-amber-600 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Katalog
                </a>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-8 relative">
            <div class="max-w-[1000px] mx-auto">

                {{-- Filter Navigasi Status Transaksi --}}
                <div class="mb-8 flex gap-3 overflow-x-auto pb-2 no-scrollbar border-b border-slate-200">
                    <a href="{{ route('customer.transactions') }}" class="pb-3 px-2 text-sm font-bold transition-all border-b-2 {{ empty($statusFilter) ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        Semua Transaksi
                    </a>
                    <a href="{{ route('customer.transactions', ['status' => 'menunggu']) }}" class="pb-3 px-2 text-sm font-bold transition-all border-b-2 {{ ($statusFilter ?? '') == 'menunggu' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        Menunggu Pembayaran
                    </a>
                    <a href="{{ route('customer.transactions', ['status' => 'diproses']) }}" class="pb-3 px-2 text-sm font-bold transition-all border-b-2 {{ ($statusFilter ?? '') == 'diproses' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        Sedang Diproses
                    </a>
                    <a href="{{ route('customer.transactions', ['status' => 'selesai']) }}" class="pb-3 px-2 text-sm font-bold transition-all border-b-2 {{ ($statusFilter ?? '') == 'selesai' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        Selesai
                    </a>
                    <a href="{{ route('customer.transactions', ['status' => 'gagal']) }}" class="pb-3 px-2 text-sm font-bold transition-all border-b-2 {{ ($statusFilter ?? '') == 'gagal' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        Dibatalkan
                    </a>
                </div>

                {{-- List Card Transaksi --}}
                <div class="space-y-6">
                    @forelse($transactions as $trx)
                        @php
                            $totalTagihan = $trx->total_amount ?? $trx->gross_amount ?? $trx->total_price ?? 0;
                            if ($totalTagihan <= 0) {
                                foreach($trx->details ?? [] as $detail) {
                                    $totalTagihan += ($detail->qty * ($detail->price ?? 0));
                                }
                            }
                            $dbStatus = strtolower(trim($trx->status ?? 'pending'));
                        @endphp

                        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden hover:border-amber-200 transition-colors">
                            
                            {{-- Header Card Transaksi --}}
                            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center flex-wrap gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d M Y, H:i') }}</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $trx->invoice_number ?? 'INV-'.$trx->id }}</p>
                                    </div>
                                </div>
                                
                                {{-- BADGE STATUS CUSTOMER --}}
                                <div>
                                    @if(in_array($dbStatus, ['pending', 'unpaid', 'menunggu pembayaran']))
                                        <span class="bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider border border-amber-200">
                                            <i class="fa-regular fa-clock mr-1"></i> Menunggu Bayar
                                        </span>
                                    @elseif(in_array($dbStatus, ['processing', 'diproses', 'sedang diproses']))
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider border border-blue-200">
                                            <i class="fa-solid fa-box-open mr-1"></i> Sedang Diproses
                                        </span>
                                    @elseif(in_array($dbStatus, ['success', 'selesai', 'settlement', 'paid']))
                                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider border border-emerald-200">
                                            <i class="fa-solid fa-check mr-1"></i> Selesai
                                        </span>
                                    @else
                                        <span class="bg-rose-100 text-rose-700 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider border border-rose-200">
                                            <i class="fa-solid fa-xmark mr-1"></i> Dibatalkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Rincian Suku Cadang yang Dibeli --}}
                            <div class="p-6">
                                @foreach($trx->details ?? [] as $detail)
                                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-50 last:mb-0 last:pb-0 last:border-0">
                                        {{-- 🚀 KUNCI FIX: Memanggil Jalur Foto Asli Database Kelompok --}}
                                        <div class="w-16 h-16 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            @php
                                                $trxPathFoto = DB::table('product_images')->where('product_id', $detail->product_id)->value('image_path');
                                            @endphp
                                            @if($trxPathFoto)
                                                <img src="{{ asset('storage/' . $trxPathFoto) }}" class="max-w-full max-h-full object-contain p-1">
                                            @else
                                                <i class="fa-solid fa-box text-slate-300 text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-slate-800 line-clamp-1">{{ $detail->product->name ?? 'Produk Suku Cadang' }}</h4>
                                            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $detail->qty }} x Rp {{ number_format($detail->price ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right pl-4">
                                            <p class="text-sm font-black text-slate-900">Rp {{ number_format(($detail->qty * ($detail->price ?? 0)), 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Footer Card Transaksi --}}
                            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Tagihan</p>
                                    <p class="text-lg font-black text-amber-600">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex gap-3 items-center">
                                    <a href="{{ route('customer.invoice', $trx->invoice_number ?? 'INV-'.$trx->id) }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
                                        <i class="fa-solid fa-file-invoice text-slate-400"></i> Lihat Nota
                                    </a>

                                    @if(in_array($dbStatus, ['pending', 'unpaid', 'menunggu', 'menunggu pembayaran']) && !empty($trx->snap_token))
                                        <button onclick="payTransaction('{{ $trx->snap_token }}')" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-sm">
                                            <i class="fa-solid fa-wallet mr-2"></i> Bayar Sekarang
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                <i class="fa-solid fa-receipt text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-800 mb-1">Belum Ada Transaksi</h3>
                            <p class="text-sm font-medium text-slate-500">Ayo mulai belanja suku cadang berkualitas di Partlyfe!</p>
                            <a href="{{ route('customer.dashboard') }}" class="inline-block mt-6 bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl text-sm transition-all shadow-sm">
                                Mulai Belanja
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-10 mb-6 flex justify-center customer-pagination-container">
                    {{ $transactions->appends(['status' => $statusFilter ?? ''])->links() }}
                </div>

            </div>
        </main>
    </div>

    <script>
        function payTransaction(snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: function(result) { window.location.reload(); },
                onPending: function(result) { window.location.reload(); },
                onError: function(result) { window.location.reload(); },
                onClose: function() { console.log('Tunda pembayaran.'); }
            });
        }
    </script>
</body>
</html>