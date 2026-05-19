    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Partlyfe | Ekosistem Suku Cadang Masa Depan</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <!-- ZONA FLEXING CSS CUSTOM (ANIMASI DEWA) -->
        <style>
            /* Animasi Blob Bergerak */
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 { animation-delay: 2s; }
            .animation-delay-4000 { animation-delay: 4s; }

            /* Animasi Teks Berjalan (Marquee) */
            @keyframes marquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-100%); }
            }
            .animate-marquee {
                display: inline-block;
                white-space: nowrap;
                animation: marquee 20s linear infinite;
            }

            /* Efek Kaca (Glassmorphism) */
            .glass-panel {
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            }

            /* Gradient Text Animasi */
            .text-gradient-animate {
                background-size: 200% auto;
                color: transparent;
                background-clip: text;
                -webkit-background-clip: text;
                animation: gradient-shift 3s ease infinite;
            }
            @keyframes gradient-shift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            body { background-color: #020617; color: white; overflow-x: hidden; }
        </style>
    </head>
    <body class="antialiased selection:bg-amber-500 selection:text-slate-900">

        <!-- HEADER GLASSMORPHISM -->
        <header class="fixed top-0 w-full z-50 glass-panel border-b-0 border-white/10 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <a href="/" class="text-3xl font-black text-white tracking-tighter" data-aos="fade-right">
                    PARTLYFE<span class="text-amber-500">.</span>
                </a>
                <div class="flex items-center gap-4" data-aos="fade-left">
                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="font-bold text-slate-300 hover:text-amber-500 transition px-4 py-2">Dashboard Utama</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-slate-300 hover:text-amber-500 transition px-4 py-2">Log In</a>
                        <a href="{{ route('register') }}" class="relative group inline-flex items-center justify-center px-6 py-2.5 font-bold text-slate-900 bg-white rounded-full overflow-hidden transition-all hover:scale-105">
                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-amber-500 rounded-full group-hover:w-56 group-hover:h-56"></span>
                            <span class="relative">Bergabung</span>
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- HERO SECTION DENGAN ANIMATED BLOBS -->
        <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
            
            <!-- Blobs Background (Flexing Element) -->
            <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-screen filter blur-[100px] opacity-50 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-amber-500 rounded-full mix-blend-screen filter blur-[100px] opacity-50 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-rose-500 rounded-full mix-blend-screen filter blur-[100px] opacity-50 animate-blob animation-delay-4000"></div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay pointer-events-none"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>

            <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
                
                <div data-aos="zoom-in" data-aos-duration="1000">
                    <span class="inline-flex items-center gap-2 glass-panel border border-amber-500/30 text-amber-400 font-bold text-xs px-5 py-2.5 rounded-full uppercase tracking-widest shadow-[0_0_20px_rgba(245,158,11,0.2)] mb-8">
                        <span class="relative flex h-2 w-2 mr-1"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span></span>
                        Ekosistem B2C & B2B Aktif
                    </span>
                </div>
                
                <h1 class="text-6xl md:text-8xl font-black text-white leading-[1.1] tracking-tighter mb-8" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="200">
                    Revolusi Suku Cadang <br>
                    <span class="text-gradient-animate bg-gradient-to-r from-amber-400 via-rose-500 to-purple-500">Era Digital.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="400">
                    Meninggalkan rantai pasok konvensional. Dapatkan suku cadang kendaraan 100% original dengan harga distributor, langsung dari ujung jarimu.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="600">
                    <a href="{{ route('customer.dashboard') }}" class="group relative px-8 py-4 font-black text-slate-900 bg-amber-500 rounded-full overflow-hidden shadow-[0_0_40px_rgba(245,158,11,0.4)] hover:shadow-[0_0_60px_rgba(245,158,11,0.6)] transition-all hover:-translate-y-1 w-full sm:w-auto">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-amber-400 to-amber-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <span class="relative flex items-center justify-center gap-2">Jelajahi Katalog <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i></span>
                    </a>
                </div>
            </div>
        </section>

        <!-- RUNNING TEXT (INFINITE MARQUEE) -->
        <div class="border-y border-white/10 bg-white/5 py-4 overflow-hidden relative backdrop-blur-sm">
            <div class="animate-marquee flex gap-12 whitespace-nowrap text-slate-400 font-bold tracking-widest uppercase text-sm">
                <span><i class="fa-solid fa-star text-amber-500"></i> 100% Garansi Original</span>
                <span><i class="fa-solid fa-bolt text-amber-500"></i> Pengiriman Instan</span>
                <span><i class="fa-solid fa-robot text-amber-500"></i> Konsultasi AI 24/7</span>
                <span><i class="fa-solid fa-shield-halved text-amber-500"></i> Pembayaran Aman</span>
                <!-- Duplicate for infinite effect -->
                <span><i class="fa-solid fa-star text-amber-500"></i> 100% Garansi Original</span>
                <span><i class="fa-solid fa-bolt text-amber-500"></i> Pengiriman Instan</span>
                <span><i class="fa-solid fa-robot text-amber-500"></i> Konsultasi AI 24/7</span>
                <span><i class="fa-solid fa-shield-halved text-amber-500"></i> Pembayaran Aman</span>
            </div>
        </div>

        <!-- BENTO GRID SECTION (FLEXING TINGKAT TINGGI) -->
        <section class="py-32 px-6 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-4xl md:text-6xl font-black mb-6" data-aos="fade-up">Kenapa <span class="text-amber-500">Partlyfe?</span></h2>
                    <p class="text-slate-400 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">Bukan sekadar toko sparepart biasa. Kami membangun ekosistem dengan teknologi terkini untuk memastikan pengalaman terbaik.</p>
                </div>

                <!-- Asymmetric Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-6 h-auto md:h-[600px]">
                    
                    <!-- Box 1 (Besar Kiri) -->
                    <div class="md:col-span-2 md:row-span-1 glass-panel rounded-3xl p-8 relative overflow-hidden group hover:border-amber-500/50 transition-colors duration-500" data-aos="fade-up" data-aos-delay="100">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl group-hover:bg-amber-500/40 transition-colors"></div>
                        <i class="fa-solid fa-microchip text-4xl text-amber-500 mb-6 relative z-10"></i>
                        <h3 class="text-2xl font-bold mb-3 relative z-10">Algoritma Prediksi Stok (ARIMA)</h3>
                        <p class="text-slate-400 relative z-10 w-2/3">Sistem kami terintegrasi dengan Machine Learning untuk memprediksi kapan suatu barang akan habis dan kapan harus restock. Kamu tidak akan pernah kehabisan barang penting.</p>
                    </div>

                    <!-- Box 2 (Kanan Atas) -->
                    <div class="md:col-span-1 md:row-span-1 glass-panel rounded-3xl p-8 relative overflow-hidden group hover:border-purple-500/50 transition-colors duration-500" data-aos="fade-up" data-aos-delay="200">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl group-hover:bg-purple-500/40 transition-colors"></div>
                        <i class="fa-solid fa-robot text-4xl text-purple-400 mb-6 relative z-10"></i>
                        <h3 class="text-2xl font-bold mb-3 relative z-10">Mekanik AI Pribadi</h3>
                        <p class="text-slate-400 relative z-10">Tanya kendala motormu kapan saja. Chatbot cerdas kami siap memberikan rekomendasi parts yang tepat 24/7.</p>
                    </div>

                    <!-- Box 3 (Kiri Bawah) -->
                    <div class="md:col-span-1 md:row-span-1 glass-panel rounded-3xl p-8 relative overflow-hidden group hover:border-rose-500/50 transition-colors duration-500" data-aos="fade-up" data-aos-delay="300">
                        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-rose-500/20 rounded-full blur-3xl group-hover:bg-rose-500/40 transition-colors"></div>
                        <h3 class="text-5xl font-black text-rose-500 mb-2 relative z-10 counter" data-target="15000">0</h3>
                        <h4 class="text-xl font-bold mb-2 relative z-10">Parts Tersedia</h4>
                        <p class="text-slate-400 text-sm relative z-10">Dari baut terkecil hingga blok mesin rakitan utuh.</p>
                    </div>

                    <!-- Box 4 (Kanan Bawah Lebar) -->
                    <div class="md:col-span-2 md:row-span-1 glass-panel rounded-3xl p-8 relative overflow-hidden group hover:border-emerald-500/50 transition-colors duration-500 flex flex-col justify-end" data-aos="fade-up" data-aos-delay="400">
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/40 to-transparent z-0"></div>
                        <div class="relative z-10">
                            <i class="fa-solid fa-truck-fast text-4xl text-emerald-400 mb-4"></i>
                            <h3 class="text-2xl font-bold mb-2">Rantai Distribusi Tanpa Batas</h3>
                            <p class="text-slate-400">Integrasi langsung ke gudang utama Sinar Jaya Motor menjamin harga tangan pertama untuk pembeli B2C maupun B2B.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- FOOTER SIMPLE ELEGANT -->
        <footer class="border-t border-white/10 bg-[#020617] pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-3xl font-black text-white mb-6">Mulai Transaksi Pertamamu.</h2>
                <a href="{{ route('customer.dashboard') }}" class="inline-block border border-white/20 text-white font-bold px-8 py-3 rounded-full hover:bg-white hover:text-slate-900 transition mb-16">
                    Buka Etalase
                </a>
                <p class="text-slate-600 text-sm">© 2026 Partlyfe Core System. Build by Lennard Lucius Huang.</p>
            </div>
        </footer>

        <!-- SCRIPTS -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            // 1. Inisialisasi AOS (Animasi Scroll)
            AOS.init({
                once: true, 
                offset: 50,
                duration: 800,
                easing: 'ease-out-cubic',
            });

            // 2. Logika Animasi JS Counter (FLEXING DOM MANIPULATION)
            const counters = document.querySelectorAll('.counter');
            const speed = 200; // Semakin kecil semakin cepat

            // Fungsi mengecek apakah elemen terlihat di layar
            const isElementInViewport = (el) => {
                const rect = el.getBoundingClientRect();
                return (rect.top >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight));
            };

            const runCounter = () => {
                counters.forEach(counter => {
                    if(isElementInViewport(counter) && !counter.classList.contains('counted')) {
                        counter.classList.add('counted'); // Cegah jalan 2x
                        const updateCount = () => {
                            const target = +counter.getAttribute('data-target');
                            const count = +counter.innerText;
                            const inc = target / speed;

                            if (count < target) {
                                counter.innerText = Math.ceil(count + inc);
                                setTimeout(updateCount, 15);
                            } else {
                                counter.innerText = target.toLocaleString('id-ID') + "+";
                            }
                        };
                        updateCount();
                    }
                });
            };

            // Jalankan saat discroll
            window.addEventListener('scroll', runCounter);
        </script>
    </body>
    </html>