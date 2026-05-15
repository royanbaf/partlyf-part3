@if(session('success') || session('error'))
    @php
        $isError = session('error');
        $msg = session('success') ?? session('error');
    @endphp
    <div id="global-toast" class="fixed top-10 left-1/2 transform -translate-x-1/2 z-[9999] flex items-center gap-4 px-6 py-4 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] backdrop-blur-md toast-enter {{ $isError ? 'bg-rose-500/10 border border-rose-500/30 text-rose-400' : 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $isError ? 'bg-rose-500/20' : 'bg-emerald-500/20' }}">
            <i class="fa-solid {{ $isError ? 'fa-xmark' : 'fa-check' }} text-lg"></i>
        </div>
        <p class="font-bold text-sm tracking-wide">{{ $msg }}</p>
    </div>

    <style>
        @keyframes slideDownFade {
            0% { opacity: 0; transform: translate(-50%, -20px); }
            100% { opacity: 1; transform: translate(-50%, 0); }
        }
        .toast-enter { animation: slideDownFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('global-toast');
            if(toast) {
                toast.style.transition = 'all 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
                toast.style.opacity = '0';
                toast.style.transform = 'translate(-50%, -20px)';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
    </script>
@endif
<style>
    body { background-color: #020617; color: white; overflow-x: hidden; }
    
    /* 1. GLASS PANEL (Header & Sidebar) - Tetap pakai blur tapi diturunkan radiusnya */
    .glass-panel { 
        background: rgba(15, 23, 42, 0.85); 
        backdrop-filter: blur(8px); 
        -webkit-backdrop-filter: blur(8px); 
        border-right: 1px solid rgba(255, 255, 255, 0.05); 
        border-bottom: 1px solid rgba(255, 255, 255, 0.05); 
        transform: translateZ(0); 
        will-change: transform; /* Jangan masukkan backdrop-filter ke will-change */
    }
    
    /* 2. GLASS CARD (Produk, Notif, dll) - THE GAME CHANGER (Tanpa Blur Berat) */
    .glass-card { 
        background: rgba(30, 41, 59, 0.9); /* Solid tapi sedikit tembus pandang */
        border: 1px solid rgba(255, 255, 255, 0.08); 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }
    
    /* 3. ANIMASI BLOB BACKGROUND - Diringankan agar GPU bernapas */
    @keyframes blob {
        0% { transform: translate3d(0px, 0px, 0px) scale(1); }
        33% { transform: translate3d(20px, -30px, 0px) scale(1.05); }
        66% { transform: translate3d(-15px, 15px, 0px) scale(0.95); }
        100% { transform: translate3d(0px, 0px, 0px) scale(1); }
    }
    .animate-blob { 
        animation: blob 25s infinite linear; 
        will-change: transform; 
        backface-visibility: hidden;
        filter: blur(60px) !important; /* Paksa batas blur global */
        opacity: 0.25 !important;
    }
    .animation-delay-2000 { animation-delay: 5s; }
    .animation-delay-4000 { animation-delay: 10s; }
</style>

<aside class="w-72 glass-panel text-slate-300 flex flex-col h-full z-20 flex-shrink-0 relative">
    <div class="h-20 flex items-center px-8 border-b border-white/5">
        <a href="{{ route('customer.dashboard') }}" class="text-3xl font-black text-white tracking-tighter">
            PARTLYFE<span class="text-amber-500">.</span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 scrollbar-hide relative z-10">
        <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-4">Menu Utama</p>
        
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('customer.dashboard') || request()->routeIs('product.detail') || request()->routeIs('customer.categories') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30 shadow-[0_0_15px_rgba(245,158,11,0.2)]' : 'hover:bg-white/5 hover:text-white border border-transparent' }}">
            <i class="fa-solid fa-store w-5 text-center text-lg"></i> Katalog Produk
        </a>

        <div class="group cursor-pointer">
            <a href="{{ Auth::check() ? route('customer.transactions') : route('login') }}" class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors font-semibold {{ request()->routeIs('customer.transactions') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30 shadow-[0_0_15px_rgba(245,158,11,0.2)]' : 'hover:bg-white/5 hover:text-white border border-transparent' }}">
                <div class="flex items-center gap-4">
                    <i class="fa-solid fa-receipt w-5 text-center text-lg"></i> Transaksi Saya
                </div>
                <i class="fa-solid fa-chevron-down text-xs transition-transform group-hover:rotate-180 text-slate-500"></i>
            </a>
        </div>

        <a href="{{ Auth::check() ? route('customer.broadcast') : route('login') }}" class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors font-semibold {{ request()->routeIs('customer.broadcast') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30 shadow-[0_0_15px_rgba(245,158,11,0.2)]' : 'hover:bg-white/5 hover:text-white border border-transparent' }}">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-tower-broadcast w-5 text-center text-lg"></i> Kabar Admin
            </div>
        </a>

        <a href="{{ Auth::check() ? route('customer.ai-chat') : route('login') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all font-semibold mt-2 relative overflow-hidden group border border-indigo-500/30 bg-indigo-500/10 hover:shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:-translate-y-0.5">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-robot w-5 text-center text-lg text-indigo-400 relative z-10"></i>
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-300 to-purple-300 relative z-10">Tanya Mekanik AI</span>
        </a>

        <p class="px-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-8">Pengaturan</p>

        <a href="{{ Auth::check() ? route('customer.profile') : route('login') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-colors font-semibold {{ request()->routeIs('customer.profile') ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'hover:bg-white/5 hover:text-white border border-transparent' }}">
            <i class="fa-solid fa-user-gear w-5 text-center text-lg"></i> Profil & Alamat
        </a>
    </div>

    @auth
        <div class="p-4 border-t border-white/5 bg-slate-900/30">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center font-bold text-slate-900 border-2 border-slate-800 shadow-[0_0_10px_rgba(245,158,11,0.5)]">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-rose-500/20 text-rose-400 font-bold hover:bg-rose-500 hover:text-white transition-colors text-sm border border-rose-500/30">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    @else
        <div class="p-4 border-t border-white/5 bg-slate-900/30 space-y-2">
            <p class="text-xs text-slate-400 text-center mb-3">Nikmati fitur lengkap</p>
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-amber-500 text-slate-900 font-bold hover:bg-amber-400 transition-colors text-sm shadow-[0_0_15px_rgba(245,158,11,0.4)]">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </a>
        </div>
    @endauth
</aside>