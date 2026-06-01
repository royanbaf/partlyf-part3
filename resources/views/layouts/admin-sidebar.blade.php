<aside class="w-72 bg-[#020617] border-r border-white/5 flex flex-col h-screen flex-shrink-0 z-[60]">
    {{-- Brand Header Panel --}}
    <div class="p-8">
        <h1 class="text-2xl font-black text-white tracking-tighter flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(99,102,241,0.5)]">
                <i class="fa-solid fa-gears text-sm"></i>
            </div>
            Partlyfe <span class="text-indigo-500 text-xs uppercase tracking-widest ml-1">Admin</span>
        </h1>
    </div>

    {{-- Navigation Links Menu --}}
    <nav class="flex-1 px-6 space-y-2 mt-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl {{ Request::is('admin/dashboard') ? 'bg-indigo-500 text-white' : 'text-slate-400 hover:bg-white/5' }} transition-all font-bold">
            <i class="fa-solid fa-chart-line w-5 text-center"></i> Overview
        </a>

        <a href="{{ url('/admin/pos') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all font-bold {{ Request::is('admin/pos*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:bg-white/5' }}">
            <i class="fa-solid fa-cash-register w-5 text-center"></i> Sistem POS Kasir
        </a>
        
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl {{ Request::is('admin/products*') ? 'bg-indigo-500 text-white' : 'text-slate-400 hover:bg-white/5' }} transition-all font-bold">
            <i class="fa-solid fa-box w-5 text-center"></i> Produk & Stok
        </a>

        <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl {{ Request::is('admin/transactions*') ? 'bg-indigo-500 text-white' : 'text-slate-400 hover:bg-white/5' }} transition-all font-bold">
            <i class="fa-solid fa-receipt w-5 text-center"></i> Transaksi
        </a>

        <a href="{{ url('/admin/customers') }}" 
           class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all font-bold
           {{ Request::is('admin/customers*') 
              ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' 
              : 'text-slate-400 hover:bg-white/5' }}">
            <i class="fa-solid fa-users w-5 text-center"></i> Pelanggan
        </a>

        <a href="{{ route('admin.broadcast.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl {{ Request::is('admin/broadcast*') ? 'bg-indigo-500 text-white' : 'text-slate-400 hover:bg-white/5' }} transition-all font-bold">
            <i class="fa-solid fa-tower-broadcast w-5 text-center"></i> Broadcast Promo
        </a>
    </nav>

    {{-- System Logout Footer Panel --}}
    <div class="p-6 border-t border-white/5">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center gap-4 px-4 py-3 rounded-xl text-rose-400 hover:bg-rose-500/10 transition-all font-bold">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Keluar Sistem
            </button>
        </form>
    </div>
</aside>