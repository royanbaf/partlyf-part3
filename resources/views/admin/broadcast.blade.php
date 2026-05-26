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

            <form action="{{ route('admin.broadcast.store') }}" method="POST" class="space-y-4">
                
                @if (session('success'))
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl flex items-start gap-3 animate-fade-in font-sans">
        <div class="text-emerald-400 mt-0.5">
            <i class="fa-solid fa-circle-check text-base"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Broadcast Sent Successfully</h4>
            <p class="text-xs text-slate-400 mt-1 font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-500 hover:text-slate-300 transition-colors">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>
@endif
    @csrf
    
    <div>
        <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Judul Promo / Notifikasi</label>
        <input type="text" name="title" required class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-amber-500">
    </div>

    <div>
        <label class="block text-xs uppercase tracking-wider text-slate-400 mb-2">Isi Pesan Broadcast</label>
        <textarea name="message" rows="4" required class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-amber-500" placeholder="Tulis info komponen diskon di sini..."></textarea>
    </div>

    <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs uppercase tracking-widest transition-colors">
        <i class="fa-solid fa-paper-plane mr-2"></i> Siarkan Sekarang
    </button>
</form>
            </form>
        </div>
    </main>
</body>
</html>