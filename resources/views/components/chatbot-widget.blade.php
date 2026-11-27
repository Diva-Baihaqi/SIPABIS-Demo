@if(\App\Models\AiSetting::isAssistantEnabled())
<div x-data="sipabisChatbot()" class="relative">
    <!-- Floating Action Button (Bottom-Right) -->
    <div class="fixed bottom-6 right-6 z-50">
        <button type="button"
                @click="toggleChat()"
                class="relative w-14 h-14 rounded-full bg-gradient-to-tr from-teal-800 via-teal-600 to-emerald-500 text-white shadow-2xl hover:shadow-teal-500/30 flex items-center justify-center transition-all duration-300 transform hover:scale-110 active:scale-95 group focus:outline-none focus:ring-4 focus:ring-teal-400/40 cursor-pointer"
                :class="{ 'rotate-90': isOpen }"
                aria-label="Buka Asisten Virtual SIPABIS">
            
            <!-- Online Pulse Indicator -->
            <span class="absolute top-0.5 right-0.5 flex h-3.5 w-3.5" x-show="!isOpen">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-brand-lime border-2 border-white"></span>
            </span>

            <!-- Bot Sparkle Icon (When Closed) -->
            <svg x-show="!isOpen" class="w-7 h-7 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>

            <!-- Close Icon (When Open) -->
            <svg x-show="isOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Chat Popover Window -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         class="fixed bottom-24 right-4 sm:right-6 z-50 w-[calc(100vw-2rem)] sm:w-[390px] h-[540px] max-h-[82vh] flex flex-col bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden font-sans"
         style="display: none;"
         @keydown.escape.window="isOpen = false">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-950 via-teal-950 to-slate-900 text-white px-4 py-3.5 flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center p-1.5 shadow-xs">
                    <x-bps-logo size="sm" />
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-sm font-bold tracking-tight text-white">Asisten SIPABIS</h3>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30">
                            Resmi
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-300 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-lime animate-pulse"></span>
                        <span>BPS Kabupaten Cirebon • Online</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <button type="button" 
                        @click="clearHistory()"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition cursor-pointer"
                        title="Bersihkan Percakapan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                <button type="button" 
                        @click="isOpen = false"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition cursor-pointer"
                        title="Tutup Chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/70" x-ref="chatBox">
            
            <!-- Default Welcome Message -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-full bg-teal-800 text-white flex items-center justify-center text-[10px] font-black shrink-0 shadow-xs">
                    AI
                </div>
                <div class="bg-white text-slate-800 border border-slate-200/80 rounded-2xl rounded-tl-xs p-3 text-[12px] shadow-xs max-w-[88%] space-y-1.5 leading-relaxed">
                    <p class="font-semibold text-slate-900">Halo! Saya <strong>Asisten SIPABIS</strong> BPS Kabupaten Cirebon.</p>
                    <p class="text-slate-600">Ada yang bisa saya bantu terkait ketersediaan stok ATK, kode BMN, alur pengajuan, atau buku kendali ruangan?</p>
                </div>
            </div>

            <!-- Quick Suggestion Prompts -->
            <div class="pt-0.5 flex flex-wrap gap-1.5" x-show="messages.length === 0">
                <button type="button" 
                        @click="sendQuickPrompt('Apakah ada kertas HVS A4 di gudang?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-white border border-teal-200/80 text-teal-800 hover:bg-teal-50 hover:border-teal-400 transition-all shadow-2xs cursor-pointer">
                    📄 Cek Stok Kertas A4
                </button>
                <button type="button" 
                        @click="sendQuickPrompt('Bagaimana alur pengajuan ATK pegawai?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-white border border-teal-200/80 text-teal-800 hover:bg-teal-50 hover:border-teal-400 transition-all shadow-2xs cursor-pointer">
                    📋 Alur Pengajuan ATK
                </button>
                <button type="button" 
                        @click="sendQuickPrompt('Bagaimana cara mengajukan usulan barang baru?')"
                        class="text-[11px] px-2.5 py-1 rounded-full bg-white border border-teal-200/80 text-teal-800 hover:bg-teal-50 hover:border-teal-400 transition-all shadow-2xs cursor-pointer">
                    ✨ Usulan Barang Baru
                </button>
            </div>

            <!-- Messages Loop -->
            <template x-for="(msg, index) in messages" :key="index">
                <div>
                    <!-- User Message -->
                    <template x-if="msg.role === 'user'">
                        <div class="flex justify-end items-end gap-1.5">
                            <div class="bg-teal-700 text-white rounded-2xl rounded-br-xs px-3.5 py-2 text-[12px] shadow-xs max-w-[85%] leading-relaxed">
                                <p x-text="msg.text" class="whitespace-pre-wrap"></p>
                            </div>
                        </div>
                    </template>

                    <!-- AI Message -->
                    <template x-if="msg.role === 'ai'">
                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-teal-800 text-white flex items-center justify-center text-[10px] font-black shrink-0 shadow-xs">
                                AI
                            </div>
                            <div class="bg-white text-slate-800 border border-slate-200/80 rounded-2xl rounded-tl-xs px-3.5 py-2.5 text-[12px] shadow-xs max-w-[88%] leading-relaxed space-y-1">
                                <div x-html="formatResponse(msg.text)" class="text-[12px] leading-relaxed text-slate-800"></div>
                                <span class="text-[9px] text-slate-400 block pt-0.5 text-right font-mono" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Loading Indicator Typing Animation -->
            <div x-show="isLoading" class="flex items-start gap-2.5" style="display: none;">
                <div class="w-7 h-7 rounded-full bg-teal-800 text-white flex items-center justify-center text-[10px] font-black shrink-0 shadow-xs">
                    AI
                </div>
                <div class="bg-white text-slate-500 border border-slate-200/80 rounded-2xl rounded-tl-xs px-3.5 py-2.5 text-xs shadow-xs flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-bounce" style="animation-delay: 300ms"></span>
                    <span class="text-[11px] text-slate-400 ml-1">Mengetik...</span>
                </div>
            </div>

        </div>

        <!-- Input Bar Footer -->
        <div class="p-3 bg-white border-t border-slate-100 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text"
                       x-model="inputMessage"
                       :disabled="isLoading"
                       maxlength="500"
                       placeholder="Tanya stok barang atau alur SIPABIS..."
                       class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[12px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition disabled:opacity-60 disabled:cursor-not-allowed">
                
                <button type="submit"
                        :disabled="isLoading || !inputMessage.trim()"
                        class="p-2 rounded-xl bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold transition shadow-xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shrink-0"
                        title="Kirim Pertanyaan">
                    <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5 px-1">
                <span>Layanan Informasi Persediaan</span>
            </div>
        </div>

    </div>
</div>

<script>
function sipabisChatbot() {
    return {
        isOpen: false,
        isLoading: false,
        inputMessage: '',
        messages: [],

        init() {
            // Muat riwayat dari sessionStorage jika ada
            try {
                const saved = sessionStorage.getItem('sipabis_chat_history');
                if (saved) {
                    this.messages = JSON.parse(saved);
                }
            } catch (e) {}
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },

        clearHistory() {
            this.messages = [];
            sessionStorage.removeItem('sipabis_chat_history');
        },

        sendQuickPrompt(text) {
            this.inputMessage = text;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.inputMessage.trim();
            if (!text || this.isLoading) return;

            const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // Tambahkan pesan user ke antarmuka
            this.messages.push({
                role: 'user',
                text: text,
                time: timeNow
            });

            this.inputMessage = '';
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('{{ route("api.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.messages.push({
                        role: 'ai',
                        text: data.reply,
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    });
                } else {
                    this.messages.push({
                        role: 'ai',
                        text: data.reply || (data.message ?? 'Maaf, terjadi kesalahan saat menghubungi asisten AI.'),
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    });
                }
            } catch (error) {
                this.messages.push({
                    role: 'ai',
                    text: 'Maaf, gagal menghubungi server asisten virtual. Periksa koneksi internet Anda.',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
                try {
                    sessionStorage.setItem('sipabis_chat_history', JSON.stringify(this.messages));
                } catch (e) {}
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.chatBox) {
                    this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
                }
            });
        },

        formatResponse(rawText) {
            if (!rawText) return '';
            let formatted = rawText
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-slate-900">$1</strong>')
                .replace(/\*(.*?)\*/g, '<em class="text-slate-700">$1</em>')
                .replace(/\n\n/g, '<div class="my-1.5"></div>')
                .replace(/\n/g, '<br>');
            return formatted;
        }
    };
}
</script>
@endif
