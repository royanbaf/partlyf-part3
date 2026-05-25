<style>
    /* Batas visual pembatas sidebar yang jelas dan tegas */
    .luxury-sidebar { 
        background: #ffffff; 
        border-right: 1px solid #e2e8f0; 
    }

    .sidebar-link-active {
        background-color: #f4f4f5 !important;
        color: #121212 !important;
        font-weight: 700;
        border-left: 3px solid #c5a880 !important;
    }
</style>

<aside id="main-sidebar" class="w-72 luxury-sidebar text-slate-400 flex flex-col h-full z-50 flex-shrink-0 relative font-sans transition-all duration-300">
    
    {{-- Brand Header Logo Partlyfe --}}
    <div class="h-20 flex items-center px-8 border-b border-slate-100">
        <a href="{{ route('customer.dashboard') }}" class="text-base font-bold tracking-[0.25em] text-slate-900 uppercase">
            PART<span class="text-[#c5a880]">LYFE</span>
        </a>
    </div>

    {{-- Daftar Menu Utama (Susunan Versi Lama, Tampilan Gaya Baru) --}}
    <div class="flex-1 overflow-y-auto py-8 px-4 space-y-1 relative z-10">
        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Menu Utama</p>
        
        {{-- Menu 1: Katalog Produk --}}
        <a href="{{ route('customer.dashboard') }}" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-widest border border-transparent transition-all duration-200 hover:bg-slate-50 hover:text-slate-900
           {{ request()->routeIs('customer.dashboard') || request()->routeIs('product.detail') ? 'sidebar-link-active' : '' }}">
            <i class="fa-solid fa-store w-5 text-center text-sm {{ request()->routeIs('customer.dashboard') || request()->routeIs('product.detail') ? 'text-[#c5a880]' : 'text-slate-400' }}"></i> 
            Katalog Produk
        </a>

        {{-- Menu 2: Transaksi Saya --}}
        <a href="{{ Auth::check() ? route('customer.transactions') : route('login') }}" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-widest border border-transparent transition-all duration-200 hover:bg-slate-50 hover:text-slate-900
           {{ request()->routeIs('customer.transactions') ? 'sidebar-link-active' : '' }}">
            <i class="fa-solid fa-receipt w-5 text-center text-sm {{ request()->routeIs('customer.transactions') ? 'text-[#c5a880]' : 'text-slate-400' }}"></i> 
            Transaksi Saya
        </a>

        {{-- Menu 3: Profil Saya --}}
        <a href="{{ Auth::check() ? route('customer.profile') : route('login') }}" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-widest border border-transparent transition-all duration-200 hover:bg-slate-50 hover:text-slate-900
           {{ request()->routeIs('customer.profile') ? 'sidebar-link-active' : '' }}">
            <i class="fa-solid fa-user-gear w-5 text-center text-sm {{ request()->routeIs('customer.profile') ? 'text-[#c5a880]' : 'text-slate-400' }}"></i> 
            Profil Saya
        </a>

        {{-- Komponen Tambahan Tombol Mekanik AI --}}
        <a href="{{ Auth::check() ? route('customer.ai-chat') : route('login') }}" 
           class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-widest transition-all duration-200 mt-4 border border-slate-200 bg-white hover:bg-slate-50 text-slate-900 shadow-sm
           {{ request()->routeIs('customer.ai-chat') ? 'border-[#c5a880] bg-[#f5f2eb]/40' : '' }}">
            <i class="fa-solid fa-robot w-5 text-center text-sm text-[#c5a880]"></i>
            <span>Mekanik AI (Full Screen)</span>
        </a>
    </div>

    {{-- Footer Area Tombol Masuk / Keluar Bawaan Kamu --}}
    @auth
        <div class="p-4 border-t border-slate-100 bg-slate-50/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300 text-xs uppercase tracking-widest">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i> Keluar
                </button>
            </form>
        </div>
    @else
        <div class="p-4 border-t border-slate-100 bg-slate-50/40">
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-[#c5a880] transition-all duration-300 text-xs uppercase tracking-widest">
                <i class="fa-solid fa-right-to-bracket text-xs"></i> Masuk
            </a>
        </div>
    @endauth
</aside>

<script>
    (function () {
        const state = localStorage.getItem('sidebarState');
        const sidebar = document.getElementById('main-sidebar');
        if (sidebar && state === 'hidden') {
            sidebar.style.transition = 'none';
            sidebar.classList.remove('w-72', 'px-4');
            sidebar.classList.add('w-0', 'overflow-hidden', 'border-r-0');
            setTimeout(() => { sidebar.style.transition = ''; }, 50);
        }
    })();
</script>