<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mekanik AI Interaktif | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* 🚀 FIX 2: MENYEMBUNYIKAN SCROLLBAR JELEK BROWSER */
        .chat-container-scroll::-webkit-scrollbar {
            width: 4px; /* Sangat tipis dan elegan */
        }
        .chat-container-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .chat-container-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0; /* Warna slate sangat tipis */
            border-radius: 10px;
        }
        
        /* Untuk menyembunyikan scrollbar di Firefox dan IE secara total jika mau */
        .no-scrollbar-raw {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .no-scrollbar-raw::-webkit-scrollbar {
            display: none;
        }

        .glass-header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            border-b: 1px solid rgba(226, 232, 240, 0.8); 
        }

        @keyframes blobFloat {
            0%   { transform: translate(0px, 0px) scale(1); }
            33%  { transform: translate(30px, -50px) scale(1.02); }
            66%  { transform: translate(-20px, 20px) scale(0.98); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; will-change: transform; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(245,158,11,0.05) 0%, transparent 70%); top: -100px; left: -100px; animation: blobFloat 15s ease-in-out infinite; }
    </style>
</head>

<body class="bg-[#f8fafc] font-sans text-slate-700 h-screen overflow-hidden flex">

    <div class="orb orb-1"></div>

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-10">

        {{-- Header --}}
        <header class="h-20 glass-header flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0">
            <div class="text-sm font-bold text-slate-400">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-amber-600 transition-colors">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-40"></i>
                <span class="text-slate-700">Mekanik AI Konsultasi</span>
            </div>
        </header>

        {{-- 🚀 FIX 1: ROMBAK TOTAL MENJADI LIGHT MODE LUXURY --}}
        <main class="flex-1 p-6 md:p-8 overflow-hidden flex flex-col justify-between max-w-[1000px] mx-auto w-full">
            
            {{-- Wadah Chat Utama --}}
            <div class="flex-1 bg-white border border-slate-200/80 rounded-3xl shadow-sm flex flex-col overflow-hidden mb-4">
                
                {{-- Top Bar Informasi Asisten --}}
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-900 shadow-sm">
                        <i class="fa-solid fa-robot text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Asisten Mekanik AI</h3>
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span> Sistem Online
                        </p>
                    </div>
                </div>

                {{-- AREA PESAN (SCROLLBAR DI-FIX AGAR TIPIS DAN HALUS) --}}
                <div id="chat-messages-box" class="flex-1 p-6 overflow-y-auto space-y-4 chat-container-scroll bg-slate-50/20">
                    
                    {{-- Pesan Pembuka Default dari AI --}}
                    <div class="flex items-start gap-3 max-w-[85%]">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0 text-xs font-bold">
                            AI
                        </div>
                        <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3.5 shadow-sm">
                            <p class="text-sm text-slate-700 leading-relaxed">
                                Halo! Saya Mekanik AI . Silakan tanyakan masalah atau gejala kerusakan pada motor Anda, saya akan bantu menganalisisnya! 👋
                            </p>
                        </div>
                    </div>

                    {{-- Pesan Dinamis Baru dari JavaScript Akan Masuk Sini --}}

                </div>

                {{-- Area Input Pesan --}}
                <div class="p-4 border-t border-slate-100 bg-white">
                    <form id="ai-chat-form" class="flex gap-3">
                        <input type="text" id="ai-user-message" placeholder="Tulis pesan atau keluhan motor Anda di sini..." autocomplete="off"
                            class="flex-1 bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none">
                        
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
                            <span>Kirim</span> <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>

            </div>

        </main>
    </div>

    {{-- JAVASCRIPT OTOMATIS AUTO-SCROLL KE BAWAH --}}
    <script>
        const chatForm = document.getElementById('ai-chat-form');
        const userMessageInput = document.getElementById('ai-user-message');
        const chatMessagesBox = document.getElementById('chat-messages-box');

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = userMessageInput.value.trim();
            if (!message) return;

            // 1. Tampilkan Pesan User di Sisi Kanan (Warna Amber Premium)
            const userRow = `
                <div class="flex items-start gap-3 max-w-[85%] ml-auto justify-end">
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl rounded-tr-none p-3.5 shadow-sm">
                        <p class="text-sm leading-relaxed">${escapeHtml(message)}</p>
                    </div>
                </div>`;
            chatMessagesBox.insertAdjacentHTML('beforeend', userRow);
            userMessageInput.value = '';
            
            scrollToBottom();

            // 2. Tampilkan Loading Status "Mekanik AI sedang mengetik..."
            const loadingId = 'ai-loading-' + Date.now();
            const loadingRow = `
                <div id="${loadingId}" class="flex items-start gap-3 max-w-[85%]">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0 text-xs font-bold">
                        AI
                    </div>
                    <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3.5 shadow-sm flex items-center gap-2 text-slate-400 text-sm">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-500"></i>
                        <span>Mekanik sedang mengetik...</span>
                    </div>
                </div>`;
            chatMessagesBox.insertAdjacentHTML('beforeend', loadingRow);
            scrollToBottom();

            // 3. AJAX: Tembak ke fungsi sendAiMessage di backend
            try {
                // Gunakan POST dan kirim data format parameter 'message' sesuai keinginan Controller kamu
                const response = await fetch("{{ route('customer.ai-chat.send') }}", { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: message })
                });
                
                const result = await response.json();
                
                // Hapus indikator loading
                document.getElementById(loadingId).remove();

                if (result.status === 'success' || result.reply) {
                    // 🚀 BACA DATA BALASAN ASLI: Menggunakan properti 'reply' sesuai controller lamamu!
                    const aiRow = `
                        <div class="flex items-start gap-3 max-w-[85%]">
                            <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center text-slate-900 flex-shrink-0 text-xs font-black">
                                AI
                            </div>
                            <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-none p-3.5 shadow-sm">
                                <p class="text-sm text-slate-700 leading-relaxed font-medium">${result.reply}</p>
                            </div>
                        </div>`;
                    chatMessagesBox.insertAdjacentHTML('beforeend', aiRow);
                } else {
                    throw new Error(result.reply || "Gagal memproses pesan.");
                }

            } catch (error) {
                console.error('Error chat AI:', error);
                document.getElementById(loadingId).remove();
                
                const errorRow = `
                    <div class="flex items-start gap-3 max-w-[85%]">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 text-xs font-bold">
                            !
                        </div>
                        <div class="bg-rose-50 border border-rose-100 rounded-2xl rounded-tl-none p-3.5 text-rose-700 text-xs font-semibold">
                            Waduh, koneksi ke mekanik terputus. Coba kirim ulang pesan Anda.
                        </div>
                    </div>`;
                chatMessagesBox.insertAdjacentHTML('beforeend', errorRow);
            }

            scrollToBottom();
        });

        function scrollToBottom() {
            chatMessagesBox.scrollTo({ top: chatMessagesBox.scrollHeight, behavior: 'smooth' });
        }

        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>