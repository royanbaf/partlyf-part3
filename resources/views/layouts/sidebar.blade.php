@if(session('success') || session('error'))
    @php
        $isError = session('error');
        $msg = session('success') ?? session('error');
    @endphp
    <div id="global-toast" class="fixed top-10 left-1/2 transform -translate-x-1/2 z-[9999] flex items-center gap-4 px-6 py-4 rounded-2xl shadow-[0_10px_40px_rgba(148,163,184,0.15)] backdrop-blur-md toast-enter {{ $isError ? 'bg-rose-50 border border-rose-200 text-rose-600' : 'bg-emerald-50 border border-emerald-200 text-emerald-600' }}">
        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $isError ? 'bg-rose-100' : 'bg-emerald-100' }}">
            <i class="fa-solid {{ $isError ? 'fa-xmark' : 'fa-check' }} text-sm"></i>
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
    body { background-color: #f8fafc !important; color: #334155 !important; }
    .luxury-sidebar { background: #ffffff; border-right: 1px solid #e2e8f0; }
</style>

<aside class="w-72 luxury-sidebar text-slate-600 flex flex-col h-full z-20 flex-shrink-0 relative">
    <div class="h-20 flex items-center px-8 border-b border-slate-100">
        <a href="{{ route('customer.dashboard') }}" class="text-2xl font-black text-slate-800 tracking-tighter">
            PARTLYFE<span class="text-amber-500">.</span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 relative z-10">
        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2">Menu Utama</p>
        
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all {{ request()->routeIs('customer.dashboard') || request()->routeIs('product.detail') || request()->routeIs('customer.cart') || request()->routeIs('customer.checkout') ? 'bg-amber-500/10 text-amber-800 border border-amber-500/20 shadow-sm' : 'hover:bg-slate-50 text-slate-600 hover:text-slate-900 border border-transparent' }}">
            <i class="fa-solid fa-store w-5 text-center text-sm"></i> Katalog Produk
        </a>

        <a href="{{ Auth::check() ? route('customer.transactions') : route('login') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all {{ request()->routeIs('customer.transactions') ? 'bg-amber-500/10 text-amber-800 border border-amber-500/20 shadow-sm' : 'hover:bg-slate-50 text-slate-600 hover:text-slate-900 border border-transparent' }}">
            <i class="fa-solid fa-receipt w-5 text-center text-sm"></i> Transaksi Saya
        </a>

        <a href="{{ Auth::check() ? route('customer.profile') : route('login') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all {{ request()->routeIs('customer.profile') ? 'bg-amber-500/10 text-amber-800 border border-amber-500/20 shadow-sm' : 'hover:bg-slate-50 text-slate-600 hover:text-slate-900 border border-transparent' }}">
            <i class="fa-solid fa-user-gear w-5 text-center text-sm"></i> Profil Saya
        </a>
    </div>

    @auth
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 font-bold hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-colors text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    @else
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 text-slate-900 font-bold hover:bg-amber-600 hover:text-white transition-colors text-xs uppercase tracking-wider shadow-sm">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </a>
        </div>
    @endauth
</aside>