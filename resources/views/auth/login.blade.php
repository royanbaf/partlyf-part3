<x-guest-layout>
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 bg-white rounded-[24px] shadow-xl shadow-gray-200/40 overflow-hidden border border-gray-100 font-sans">
        
        <div class="md:col-span-5 bg-[#121212] p-10 flex flex-col justify-between relative overflow-hidden text-white min-h-[280px] md:min-h-full">
            <div class="absolute inset-0 opacity-5 bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:40px_40px]"></div>
            
            <div class="relative z-10">
                <a href="/" class="text-sm font-bold tracking-[0.3em] uppercase text-white">
                    PART<span class="text-[#c5a880]">LYFE</span>
                </a>
            </div>

            <div class="relative z-10 space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#c5a880] block">Premium Collection</span>
                <h3 class="text-2xl font-light tracking-tight leading-tight">
                    Komponen Terbaik. <br><span class="font-semibold text-[#c5a880]">Ketahanan Tanpa Kompromi.</span>
                </h3>
            </div>

            <div class="relative z-10 text-[10px] text-gray-500 font-medium tracking-widest uppercase">
                Premium Automotive Hub
            </div>
        </div>

        <div class="md:col-span-7 p-8 sm:p-12 flex flex-col justify-center bg-white">
            <div class="text-right text-xs text-gray-400 mb-8 font-medium">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-[#c5a880] hover:text-[#b0926a] font-semibold ml-1 transition-colors">Daftar sekarang</a>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Selamat Datang Kembali</h2>
                <p class="text-xs text-gray-400 mt-1.5 font-light">Silakan masuk untuk mengakses kembali ruang manajemen suku cadang Anda.</p>
            </div>

            <x-auth-session-status class="mb-4 text-xs font-medium text-emerald-600 bg-emerald-50 border border-emerald-100 p-3 rounded-xl" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Email Address</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                        placeholder="nama@email.com"
                        class="w-full bg-[#fbfbfa] border border-gray-200 text-sm text-gray-900 px-4 py-3 rounded-xl focus:outline-none focus:border-[#c5a880] focus:bg-white transition-all placeholder-gray-300 font-medium shadow-none focus:ring-0" />
                    <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                        placeholder="••••••••"
                        class="w-full bg-[#fbfbfa] border border-gray-200 text-sm text-gray-900 px-4 py-3 rounded-xl focus:outline-none focus:border-[#c5a880] focus:bg-white transition-all placeholder-gray-300 font-medium shadow-none focus:ring-0" />
                    <x-input-error :messages="$errors->get('password')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-200 text-[#c5a880] focus:ring-0 focus:ring-offset-0 w-4 h-4 cursor-pointer" name="remember">
                        <span class="ms-2 text-gray-400 font-medium">Ingat saya di perangkat ini</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-gray-400 hover:text-[#c5a880] transition font-medium" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-8 py-3.5 text-xs font-semibold uppercase tracking-widest text-white bg-gray-900 hover:bg-[#c5a880] transition-colors duration-300 rounded-xl shadow-md shadow-gray-900/10">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>