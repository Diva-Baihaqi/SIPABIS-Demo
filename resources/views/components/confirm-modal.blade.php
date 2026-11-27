<div x-data="globalConfirmModal()" 
     x-cloak 
     @keydown.escape.window="close()" 
     class="relative z-50">

    <!-- Backdrop with Smooth Blur & Fade -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-md"
         style="display: none;"></div>

    <!-- Modal Dialog Window -->
    <div x-show="isOpen" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
         @click.self="close()"
         style="display: none;">

        <div x-show="isOpen"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100/80 overflow-hidden text-center p-6 sm:p-8"
             style="display: none;">

            <!-- Decorative Top Gradient Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5"
                 :class="{
                     'bg-gradient-to-r from-rose-500 via-red-500 to-rose-600': type === 'danger',
                     'bg-gradient-to-r from-amber-400 via-orange-500 to-amber-500': type === 'warning',
                     'bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600': type === 'success',
                     'bg-gradient-to-r from-bps-navy-600 via-teal-500 to-brand-lime': type === 'info'
                 }"></div>

            <!-- Dynamic Animated Icon -->
            <div class="relative mx-auto mb-5">
                <!-- Background Pulse Rings -->
                <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center shadow-lg transition-transform duration-300 transform hover:scale-110"
                     :class="{
                         'bg-rose-50 text-rose-600 border border-rose-100 shadow-rose-500/20': type === 'danger',
                         'bg-amber-50 text-amber-600 border border-amber-100 shadow-amber-500/20': type === 'warning',
                         'bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-emerald-500/20': type === 'success',
                         'bg-slate-50 text-bps-navy-700 border border-slate-200 shadow-slate-500/10': type === 'info'
                     }">
                    
                    <!-- Danger Icon (Shield / Exclamation) -->
                    <template x-if="type === 'danger'">
                        <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>

                    <!-- Warning Icon -->
                    <template x-if="type === 'warning'">
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>

                    <!-- Success Icon -->
                    <template x-if="type === 'success'">
                        <svg class="w-8 h-8 animate-pulse text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>

                    <!-- Info Icon -->
                    <template x-if="type === 'info'">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                </div>
            </div>

            <!-- Title & Description -->
            <h3 class="text-lg font-black text-slate-900 mb-2.5 tracking-tight" x-text="title"></h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium mb-6" x-text="message"></p>

            <!-- Action Buttons -->
            <div class="flex items-center justify-center gap-3">
                <button type="button" 
                        @click="close()" 
                        class="w-1/2 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-bold text-xs transition duration-150">
                    <span x-text="cancelText"></span>
                </button>
                <button type="button" 
                        @click="confirm()" 
                        class="w-1/2 py-2.5 px-4 rounded-2xl text-white font-bold text-xs shadow-md transition transform hover:scale-[1.02] active:scale-[0.98] duration-150"
                        :class="{
                            'bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 shadow-rose-500/25': type === 'danger',
                            'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 shadow-amber-500/25': type === 'warning',
                            'bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-emerald-500/25': type === 'success',
                            'bg-gradient-to-r from-bps-navy-800 to-slate-900 hover:bg-slate-800 shadow-slate-900/20': type === 'info'
                        }">
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('globalConfirmModal', () => ({
            isOpen: false,
            title: 'Konfirmasi Tindakan',
            message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            type: 'danger', // danger, warning, success, info
            confirmText: 'Ya, Lanjutkan',
            cancelText: 'Batal',
            onConfirmCallback: null,

            init() {
                window.showConfirmDialog = (options = {}) => {
                    this.title = options.title || 'Konfirmasi Tindakan';
                    this.message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                    this.type = options.type || 'danger';
                    this.confirmText = options.confirmText || 'Ya, Lanjutkan';
                    this.cancelText = options.cancelText || 'Batal';
                    this.onConfirmCallback = options.onConfirm || null;
                    this.isOpen = true;
                };
            },

            close() {
                this.isOpen = false;
                this.onConfirmCallback = null;
            },

            confirm() {
                const cb = this.onConfirmCallback;
                this.close();
                if (typeof cb === 'function') {
                    cb();
                }
            }
        }));
    });

    // Global Form & Button Interceptor for [data-confirm]
    document.addEventListener('DOMContentLoaded', () => {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form && form.hasAttribute('data-confirm') && !form.dataset.confirmed) {
                e.preventDefault();
                const message = form.getAttribute('data-confirm');
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const type = form.getAttribute('data-confirm-type') || 'danger';
                const confirmText = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const cancelText = form.getAttribute('data-cancel-btn') || 'Batal';

                window.showConfirmDialog({
                    title,
                    message,
                    type,
                    confirmText,
                    cancelText,
                    onConfirm: () => {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            }
        }, true);

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-confirm]');
            if (!btn || btn.tagName.toLowerCase() === 'form') return;

            const form = btn.closest('form');
            if (form && !form.dataset.confirmed) {
                e.preventDefault();
                e.stopPropagation();

                const message = btn.getAttribute('data-confirm');
                const title = btn.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const type = btn.getAttribute('data-confirm-type') || 'danger';
                const confirmText = btn.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const cancelText = btn.getAttribute('data-cancel-btn') || 'Batal';

                window.showConfirmDialog({
                    title,
                    message,
                    type,
                    confirmText,
                    cancelText,
                    onConfirm: () => {
                        form.dataset.confirmed = 'true';
                        if (btn.name) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = btn.name;
                            hiddenInput.value = btn.value;
                            form.appendChild(hiddenInput);
                        }
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit(btn);
                        } else {
                            form.submit();
                        }
                    }
                });
            }
        }, true);
    });
</script>
