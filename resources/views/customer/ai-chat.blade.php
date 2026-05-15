<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mekanik AI | Partlyfe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#020617] font-sans text-slate-200 h-screen overflow-hidden flex selection:bg-indigo-500 selection:text-white">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <div class="absolute top-1/4 left-1/2 w-[600px] h-[600px] bg-indigo-600/10 rounded-full filter blur-[100px] animate-pulse pointer-events-none z-0 transform -translate-x-1/2"></div>

        <header class="h-20 glass-panel flex items-center justify-between px-8 flex-shrink-0 z-50 sticky top-0 border-b border-white/5">
            <h2 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 flex items-center gap-3">
                <i class="fa-solid fa-robot text-indigo-400"></i> Mekanik AI
            </h2>
        </header>

        <main id="chat-container" class="flex-1 overflow-y-auto p-8 scrollbar-hide relative z-10 w-full max-w-[800px] mx-auto flex flex-col gap-6">
            
            <div class="flex gap-4 max-w-[85%]">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0 text-indigo-400">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="glass-card rounded-2xl rounded-tl-sm p-4 text-sm text-slate-300 leading-relaxed border-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                    <p>Sistem AI Partlyfe siap! 🚀</p>
                    <p class="mt-2">Saya adalah asisten virtual yang dirancang untuk membantu Anda menemukan sparepart yang tepat atau memberikan diagnosa awal untuk masalah kendaraan Anda. Ada yang bisa saya bantu?</p>
                </div>
            </div>

            </main>

        <div class="h-24 glass-panel border-t border-white/5 flex items-center justify-center px-8 flex-shrink-0 z-50 relative">
            <form id="chat-form" class="w-full max-w-[800px] relative">
                <input type="text" id="chat-input" placeholder="Tanya sesuatu tentang sparepart atau kendala motormu..." autocomplete="off" class="w-full bg-slate-900/60 border border-white/10 rounded-2xl py-4 pl-6 pr-16 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-500 shadow-[0_0_20px_rgba(0,0,0,0.3)]">
                
                <button type="submit" id="send-btn" class="absolute right-2 top-2 bottom-2 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-500 transition-colors shadow-[0_0_15px_rgba(79,70,229,0.4)] disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatContainer = document.getElementById('chat-container');
        const sendBtn = document.getElementById('send-btn');

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            // 1. Tampilkan Pesan User di Layar
            appendUserMessage(message);
            chatInput.value = '';
            chatInput.focus();
            
            // Tampilkan animasi typing AI
            const typingIndicatorId = appendAiTyping();
            scrollToBottom();

            try {
                // 2. Kirim Pesan ke Backend Laravel (AJAX Fetch)
                const response = await fetch("{{ route('customer.ai-chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                // 3. Hapus animasi typing dan tampilkan balasan AI
                document.getElementById(typingIndicatorId).remove();
                appendAiMessage(data.reply);
                
            } catch (error) {
                document.getElementById(typingIndicatorId).remove();
                appendAiMessage("Maaf, koneksi ke server AI terputus. Silakan coba lagi.");
            }
            
            scrollToBottom();
        });

        function appendUserMessage(text) {
            const html = `
                <div class="flex gap-4 max-w-[85%] self-end flex-row-reverse">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center flex-shrink-0 text-amber-500 font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="glass-card bg-slate-800/80 rounded-2xl rounded-tr-sm p-4 text-sm text-white leading-relaxed border-white/5">
                        <p>${text}</p>
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', html);
        }

        function appendAiMessage(text) {
            const html = `
                <div class="flex gap-4 max-w-[85%]">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0 text-indigo-400">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="glass-card rounded-2xl rounded-tl-sm p-4 text-sm text-slate-300 leading-relaxed border-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                        <p>${text}</p>
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', html);
        }

        function appendAiTyping() {
            const id = 'typing-' + Date.now();
            const html = `
                <div id="${id}" class="flex gap-4 max-w-[85%]">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0 text-indigo-400">
                        <i class="fa-solid fa-robot text-sm"></i>
                    </div>
                    <div class="glass-card rounded-2xl rounded-tl-sm px-5 py-4 border-indigo-500/20 flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', html);
            return id;
        }

        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</body>
</html>