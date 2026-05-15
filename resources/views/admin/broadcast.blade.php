<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Broadcast Promo | Partlyfe Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] text-slate-200 flex h-screen overflow-hidden">
    @include('layouts.admin-sidebar')
    <main class="flex-1 flex items-center justify-center p-10 relative">
        <div class="absolute inset-0 bg-indigo-500/5 filter blur-[150px] pointer-events-none"></div>

        <div class="w-full max-w-xl bg-slate-900/60 backdrop-blur-xl p-10 rounded-[40px] border border-white/10 shadow-2xl relative z-10">
            <div class="text-center mb-8">
                <i class="fa-solid fa-tower-broadcast text-4xl text-indigo-500 mb-4"></i>
                <h2 class="text-2xl font-black text-white uppercase tracking-tight">Kirim Broadcast</h2>
                <p class="text-xs text-slate-500">Pesan akan muncul di dashboard semua pelanggan.</p>
            </div>

            <form action="{{ route('admin.broadcast.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Judul Promo</label>
                    <input type="text" name="title" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-indigo-500 transition shadow-inner" placeholder="Diskon Akhir Tahun 50%!">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Isi Pesan</label>
                    <textarea name="message" rows="4" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-indigo-500 transition resize-none shadow-inner" placeholder="Detail promo dan syarat ketentuan..."></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-black py-5 rounded-2xl transition shadow-[0_10px_30px_rgba(99,102,241,0.3)] hover:-translate-y-1">
                    SEBARKAN SEKARANG
                </button>
            </form>
        </div>
    </main>
</body>
</html>