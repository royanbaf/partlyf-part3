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
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#c5a880] block">Ecosystem Registry</span>
                <h3 class="text-2xl font-light tracking-tight leading-tight">
                    Akses Langsung. <br><span class="font-semibold text-[#c5a880]">Transparansi Distributor.</span>
                </h3>
            </div>

            <div class="relative z-10 text-[10px] text-gray-500 font-medium tracking-widest uppercase">
                Premium Automotive Hub
            </div>
        </div>

        <div class="md:col-span-7 p-8 sm:p-12 flex flex-col justify-center bg-white">
            <div class="text-right text-xs text-gray-400 mb-8 font-medium">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-[#c5a880] hover:text-[#b0926a] font-semibold ml-1 transition-colors">Masuk</a>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Buat Akun Baru</h2>
                <p class="text-xs text-gray-400 mt-1.5 font-light">Bergabunglah untuk mendapatkan kemudahan manajemen komponen dan katalog berkualitas tinggi.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label for="name" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Nama Lengkap</label>
                    <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" 
                        placeholder="Nama Lengkap Anda"
                        class="w-full bg-[#fbfbfa] border border-gray-200 text-sm text-gray-900 px-4 py-3 rounded-xl focus:outline-none focus:border-[#c5a880] focus:bg-white transition-all placeholder-gray-300 font-medium shadow-none focus:ring-0" />
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Email Address</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" 
                        placeholder="nama@email.com"
                        class="w-full bg-[#fbfbfa] border border-gray-200 text-sm text-gray-900 px-4 py-3 rounded-xl focus:outline-none focus:border-[#c5a880] focus:bg-white transition-all placeholder-gray-300 font-medium shadow-none focus:ring-0" />
                    <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" 
                        placeholder="Minimal 8 Karakter"
                        class="w-full bg-[#fbfbfa] border border-gray-200 text-sm text-gray-900 px-4 py-3 rounded-xl focus:outline-none focus:border-[#c5a880] focus:bg-white transition-all placeholder-gray-300 font-medium shadow-none focus:ring-0" />
                    <x-input-error :messages="$errors->get('password')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                        placeholder="Ulangi Password"
                        class="w-full bg-[#fbfbfa] border border-gray-200 text-sm text-gray-900 px-4 py-3 rounded-xl focus:outline-none focus:border-[#c5a880] focus:bg-white transition-all placeholder-gray-300 font-medium shadow-none focus:ring-0" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-8 py-3.5 text-xs font-semibold uppercase tracking-widest text-white bg-gray-900 hover:bg-[#c5a880] transition-colors duration-300 rounded-xl shadow-md shadow-gray-900/10">
                        Sign Up
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>