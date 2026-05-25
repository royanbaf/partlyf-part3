<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broadcast Promo | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden">
    @include('layouts.admin-sidebar')
    
    <main class="flex-1 flex flex-col items-center justify-center p-10 relative">
        <div class="absolute inset-0 bg-indigo-500/5 filter blur-[150px] pointer-events-none"></div>

        {{-- Alert Notifikasi Sukses / Gagal --}}
        <div class="w-full max-w-xl mb-4">
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-circle-check text-base"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-circle-exclamation text-base"></i> {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="w-full max-w-xl bg-slate-900/60 backdrop-blur-xl p-10 rounded-[40px] border border-white/10 shadow-2xl relative z-10">
            <div class="text-center mb-8">
                <i class="fa-solid fa-tower-broadcast text-4xl text-indigo-500 mb-4 animate-pulse"></i>
                <h2 class="text-2xl font-black text-white uppercase tracking-tight">Kirim Broadcast</h2>
                <p class="text-xs text-slate-500 mt-1">Pesan akan muncul di dashboard semua pelanggan (<span class="text-indigo-400 font-bold">{{ $customerCount ?? 0 }} Akun</span>).</p>
            </div>

            <form action="{{ route('admin.broadcast.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Judul Promo *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-indigo-500 transition shadow-inner text-sm" placeholder="Diskon Akhir Tahun 50%!">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Isi Pesan *</label>
                    <textarea name="message" rows="4" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-indigo-500 transition resize-none shadow-inner text-sm" placeholder="Detail promo dan syarat ketentuan...">{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-black py-5 rounded-2xl transition shadow-[0_10px_30px_rgba(99,102,241,0.3)] hover:-translate-y-1 text-sm uppercase tracking-wider">
                    SEBARKAN SEKARANG
                </button>
            </form>
        </div>
    </main>
</body>
</html>