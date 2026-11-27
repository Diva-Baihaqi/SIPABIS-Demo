@props([
    'name' => 'foto',
    'label' => 'Foto Barang (Opsional)',
    'currentUrl' => null,
    'hasExisting' => false,
    'allowRemove' => false,
    'hint' => 'Pilih dari galeri, jepret kamera, drag & drop ke kotak ini, atau tempel gambar (Ctrl+V). Otomatis dikompres ke ukuran ringan (50–100 KB).',
])

<div x-data="{
    previewUrl: null,
    fileName: '',
    fileSizeText: '',
    uploadSource: '',
    uploadError: '',
    uploadNotice: '',
    isDragging: false,
    dragDepth: 0,
    removeExisting: false,
    cameraOpen: false,
    cameraError: '',
    stream: null,
    maxFileSizeBytes: 10 * 1024 * 1024,
    acceptedMimes: ['image/jpeg', 'image/png', 'image/webp'],

    clearFeedback() {
        this.uploadError = '';
        this.uploadNotice = '';
    },

    buildTimestampName(prefix, ext) {
        const now = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        const stamp = now.getFullYear()
            + pad(now.getMonth() + 1)
            + pad(now.getDate())
            + '_'
            + pad(now.getHours())
            + pad(now.getMinutes())
            + pad(now.getSeconds());
        return prefix + '_' + stamp + '.' + ext;
    },

    isImageFile(file) {
        if (file && file.type && file.type.startsWith('image/')) {
            return true;
        }
        return Boolean(file && /\.(jpe?g|png|webp|gif|bmp|avif)$/i.test(file.name || ''));
    },

    assignFileToInput(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        this.$refs.fileInput.files = dt.files;
    },

    convertImageToPng(file) {
        return new Promise((resolve, reject) => {
            const objectUrl = URL.createObjectURL(file);
            const img = new Image();
            img.onload = () => {
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth || img.width;
                    canvas.height = img.naturalHeight || img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    URL.revokeObjectURL(objectUrl);
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            reject(new Error('Konversi gambar gagal'));
                            return;
                        }
                        const baseName = (file.name || 'gambar').replace(/\.[^.]+$/, '');
                        resolve(new File([blob], baseName + '.png', { type: 'image/png' }));
                    }, 'image/png');
                } catch (e) {
                    URL.revokeObjectURL(objectUrl);
                    reject(e);
                }
            };
            img.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(new Error('Berkas gambar tidak dapat dibaca'));
            };
            img.src = objectUrl;
        });
    },

    async processIncomingFile(file, sourceLabel = 'Galeri', syncInput = true) {
        this.clearFeedback();

        if (!file || !this.isImageFile(file)) {
            this.uploadError = 'Berkas harus berupa gambar (JPG, PNG, atau WebP).';
            if (!syncInput && this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
            return;
        }

        if (file.size > this.maxFileSizeBytes) {
            this.uploadError = 'Ukuran gambar melebihi batas maksimal 10 MB.';
            if (!syncInput && this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
            return;
        }

        let finalFile = file;
        let mustSync = syncInput;

        if (!this.acceptedMimes.includes(file.type)) {
            try {
                finalFile = await this.convertImageToPng(file);
                mustSync = true;
            } catch (err) {
                this.uploadError = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP.';
                if (!syncInput && this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
                return;
            }
        }

        if (finalFile.size > this.maxFileSizeBytes) {
            this.uploadError = 'Ukuran gambar melebihi batas maksimal 10 MB.';
            return;
        }

        if (mustSync) {
            this.assignFileToInput(finalFile);
        }

        this.uploadSource = sourceLabel;
        this.applySelectedFile(finalFile);
    },

    handleFileChange(event) {
        const file = event.target.files[0];
        if (!file) return;
        this.processIncomingFile(file, 'Galeri', false);
    },

    applySelectedFile(file) {
        if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
            URL.revokeObjectURL(this.previewUrl);
        }
        this.previewUrl = URL.createObjectURL(file);
        this.fileName = file.name;
        const kb = Math.round(file.size / 1024);
        this.fileSizeText = kb >= 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb + ' KB';
        this.removeExisting = false;
    },

    hasDraggedFiles(event) {
        const types = event.dataTransfer ? Array.from(event.dataTransfer.types || []) : [];
        return types.includes('Files');
    },

    handleDragEnter(event) {
        if (!this.hasDraggedFiles(event)) return;
        this.dragDepth += 1;
        this.isDragging = true;
    },

    handleDragOver(event) {
        if (!this.hasDraggedFiles(event)) return;
        if (event.dataTransfer) {
            event.dataTransfer.dropEffect = 'copy';
        }
        this.isDragging = true;
    },

    handleDragLeave() {
        if (!this.isDragging) return;
        this.dragDepth = Math.max(0, this.dragDepth - 1);
        if (this.dragDepth === 0) {
            this.isDragging = false;
        }
    },

    handleDrop(event) {
        this.dragDepth = 0;
        this.isDragging = false;
        const files = event.dataTransfer && event.dataTransfer.files;
        if (!files || !files.length) return;
        this.processIncomingFile(files[0], 'Drag & Drop', true);
    },

    extractClipboardImageFile(clipboardData) {
        if (!clipboardData) return null;

        if (clipboardData.items && clipboardData.items.length) {
            for (const item of clipboardData.items) {
                if (item.kind === 'file' && item.type && item.type.startsWith('image/')) {
                    const raw = item.getAsFile();
                    if (raw) {
                        const ext = (raw.type.split('/')[1] || 'png').replace('jpeg', 'jpg');
                        const name = (!raw.name || /^image\./i.test(raw.name))
                            ? this.buildTimestampName('clipboard', ext)
                            : raw.name;
                        return new File([raw], name, { type: raw.type });
                    }
                }
            }
        }

        if (clipboardData.files && clipboardData.files.length) {
            for (const f of clipboardData.files) {
                if (this.isImageFile(f)) {
                    const ext = ((f.type && f.type.split('/')[1]) || 'png').replace('jpeg', 'jpg');
                    const name = (!f.name || /^image\./i.test(f.name))
                        ? this.buildTimestampName('clipboard', ext)
                        : f.name;
                    return new File([f], name, { type: f.type || 'image/png' });
                }
            }
        }

        return null;
    },

    handleWindowPaste(event) {
        if (this.cameraOpen) return;
        const imageFile = this.extractClipboardImageFile(event.clipboardData);
        if (!imageFile) {
            return;
        }
        event.preventDefault();
        this.processIncomingFile(imageFile, 'Clipboard', true);
    },

    async pasteFromClipboard() {
        this.clearFeedback();

        if (!navigator.clipboard || typeof navigator.clipboard.read !== 'function') {
            this.uploadNotice = 'Tekan Ctrl+V (atau Cmd+V di Mac) langsung pada keyboard untuk menempel gambar dari clipboard.';
            return;
        }

        try {
            const clipboardItems = await navigator.clipboard.read();
            for (const item of clipboardItems) {
                const imageType = item.types.find((t) => t.startsWith('image/'));
                if (imageType) {
                    const blob = await item.getType(imageType);
                    const ext = (imageType.split('/')[1] || 'png').replace('jpeg', 'jpg');
                    const file = new File([blob], this.buildTimestampName('clipboard', ext), { type: imageType });
                    await this.processIncomingFile(file, 'Clipboard', true);
                    return;
                }
            }
            this.uploadError = 'Clipboard tidak berisi gambar. Salin gambar terlebih dahulu, lalu tekan Ctrl+V atau klik Tempel.';
        } catch (err) {
            this.uploadNotice = 'Izin baca clipboard belum aktif di browser ini. Cukup tekan Ctrl+V (atau Cmd+V) langsung di keyboard.';
        }
    },

    openGallery() {
        this.clearFeedback();
        const input = this.$refs.fileInput;
        input.removeAttribute('capture');
        input.click();
    },

    async openCamera() {
        this.clearFeedback();
        this.cameraError = '';
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

        // Pada HP/Tablet atau konteks HTTP biasa, langsung panggil kamera bawaan perangkat
        if (isMobile || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const camInput = this.$refs.cameraFallbackInput;
            camInput.click();
            return;
        }

        // Pada desktop modern (localhost / HTTPS), buka pratinjau webcam langsung
        try {
            this.cameraOpen = true;
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 960 } },
                audio: false
            });
            this.$nextTick(() => {
                if (this.$refs.videoPreview) {
                    this.$refs.videoPreview.srcObject = this.stream;
                }
            });
        } catch (err) {
            this.stopCamera();
            this.cameraOpen = false;
            this.$refs.cameraFallbackInput.click();
        }
    },

    handleCameraFallbackChange(event) {
        const file = event.target.files[0];
        if (!file) return;
        this.processIncomingFile(file, 'Kamera', true);
        event.target.value = '';
    },

    captureFromWebcam() {
        const video = this.$refs.videoPreview;
        const canvas = this.$refs.captureCanvas;
        if (!video || !canvas || !video.videoWidth) return;

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            if (!blob) return;
            const file = new File([blob], this.buildTimestampName('kamera', 'jpg'), { type: 'image/jpeg' });
            this.processIncomingFile(file, 'Kamera', true);
            this.closeCameraModal();
        }, 'image/jpeg', 0.92);
    },

    closeCameraModal() {
        this.stopCamera();
        this.cameraOpen = false;
    },

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
    },

    clearSelected() {
        if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
            URL.revokeObjectURL(this.previewUrl);
        }
        this.previewUrl = null;
        this.fileName = '';
        this.fileSizeText = '';
        this.uploadSource = '';
        this.clearFeedback();
        if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
        }
    },

    toggleRemoveExisting() {
        this.removeExisting = !this.removeExisting;
        if (this.removeExisting) {
            this.clearSelected();
        }
    }
}"
@paste.window="handleWindowPaste($event)"
class="sm:col-span-2">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            {{ $label }}
        </label>
        <span class="text-[10px] font-semibold text-slate-400">
            Mendukung Galeri &bull; Kamera &bull; Drag &amp; Drop &bull; Paste <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 font-mono text-[10px] text-slate-600">Ctrl+V</kbd>
        </span>
    </div>

    <div @dragenter.prevent="handleDragEnter($event)"
         @dragover.prevent="handleDragOver($event)"
         @dragleave.prevent="handleDragLeave($event)"
         @drop.prevent="handleDrop($event)"
         :class="isDragging
             ? 'border-bps-blue-500 bg-bps-blue-50/70 ring-4 ring-bps-blue-500/15'
             : 'border-slate-200/90 bg-slate-50/90 hover:border-slate-300'"
         class="relative p-4 sm:p-5 rounded-2xl border-2 border-dashed flex flex-col sm:flex-row items-start sm:items-center gap-5 transition-all duration-150">

        <!-- Overlay Visual Saat Drag & Drop Aktif -->
        <div x-show="isDragging"
             x-transition.opacity.duration.150ms
             class="pointer-events-none absolute inset-0 z-20 rounded-2xl bg-bps-blue-600/90 backdrop-blur-[2px] flex flex-col items-center justify-center text-white p-4 text-center gap-1.5"
             style="display: none;">
            <svg class="w-8 h-8 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-sm font-extrabold tracking-tight">Lepaskan gambar di sini</p>
            <p class="text-[11px] text-bps-blue-100">Format JPG, PNG, atau WebP (maks. 10 MB)</p>
        </div>

        <!-- Pratinjau Thumbnail -->
        <button type="button"
                @click="openGallery()"
                title="Klik untuk memilih gambar atau seret gambar ke kotak ini"
                class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-white border-2 border-dashed border-slate-300 hover:border-bps-blue-400 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs group cursor-pointer transition">
            <!-- 1. Pratinjau Foto Baru yang Dipilih -->
            <template x-if="previewUrl">
                <img :src="previewUrl" alt="Pratinjau Foto Baru" class="w-full h-full object-cover">
            </template>

            <!-- 2. Foto Saat Ini (Halaman Edit) -->
            @if($currentUrl)
                <template x-if="!previewUrl">
                    <img src="{{ $currentUrl }}" alt="Foto Saat Ini"
                         :class="removeExisting ? 'opacity-25 grayscale' : ''"
                         class="w-full h-full object-contain p-1.5 transition duration-200">
                </template>
            @else
                <!-- 3. Kosong / Placeholder Upload -->
                <template x-if="!previewUrl">
                    <div class="text-center p-3 text-slate-400 group-hover:text-bps-blue-500 transition">
                        <svg class="w-8 h-8 mx-auto mb-1 text-slate-300 group-hover:text-bps-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-[10px] font-semibold block leading-tight">Seret atau klik</span>
                    </div>
                </template>
            @endif

            <!-- Overlay Penanda Hapus -->
            <div x-show="removeExisting" class="absolute inset-0 bg-rose-900/60 flex items-center justify-center p-2 text-center" style="display: none;">
                <span class="text-[10px] font-bold text-white leading-tight">Akan dihapus permanen</span>
            </div>
        </button>

        <!-- Kontrol Upload, Kamera, & Clipboard -->
        <div class="flex-1 space-y-3 w-full min-w-0">
            <!-- Input File Utama Form -->
            <input type="file"
                   name="{{ $name }}"
                   x-ref="fileInput"
                   accept="image/jpeg,image/png,image/webp"
                   @change="handleFileChange($event)"
                   class="hidden">

            <!-- Input Kamera Fallback untuk Perangkat Mobile / HTTP -->
            <input type="file"
                   x-ref="cameraFallbackInput"
                   accept="image/*"
                   capture="environment"
                   @change="handleCameraFallbackChange($event)"
                   class="hidden">

            @if($allowRemove && $hasExisting)
                <input type="hidden" name="hapus_foto" :value="removeExisting ? '1' : '0'">
            @endif

            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        @click="openGallery()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 text-xs font-bold shadow-2xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-bps-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Pilih dari Galeri</span>
                </button>

                <button type="button"
                        @click="openCamera()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-2xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Ambil Foto (Kamera)</span>
                </button>

                <button type="button"
                        @click="pasteFromClipboard()"
                        title="Tempel gambar yang disalin dari clipboard (atau tekan Ctrl+V)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-bps-navy-50 hover:bg-bps-navy-100 text-bps-navy-800 border border-bps-navy-200 text-xs font-bold shadow-2xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-bps-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Tempel Gambar</span>
                    <kbd class="hidden sm:inline px-1.5 py-0.5 rounded bg-white/80 border border-bps-navy-200/80 font-mono text-[10px] font-semibold text-bps-navy-600">Ctrl+V</kbd>
                </button>

                @if($allowRemove && $hasExisting)
                    <button type="button"
                            @click="toggleRemoveExisting()"
                            :class="removeExisting ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-rose-600 border-rose-200 hover:bg-rose-50'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-bold transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span x-text="removeExisting ? 'Batal Hapus Foto' : 'Hapus Foto Saat Ini'"></span>
                    </button>
                @endif
            </div>

            <!-- Notifikasi Galat Validasi / Clipboard -->
            <div x-show="uploadError"
                 class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px]"
                 style="display: none;">
                <span class="font-medium" x-text="uploadError"></span>
                <button type="button" @click="uploadError = ''" class="text-rose-600 hover:text-rose-800 font-bold shrink-0 px-1 cursor-pointer">&times;</button>
            </div>

            <!-- Notifikasi Info Pintasan Clipboard -->
            <div x-show="uploadNotice"
                 class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px]"
                 style="display: none;">
                <span class="font-medium" x-text="uploadNotice"></span>
                <button type="button" @click="uploadNotice = ''" class="text-amber-700 hover:text-amber-900 font-bold shrink-0 px-1 cursor-pointer">&times;</button>
            </div>

            <!-- Info Berkas Terpilih -->
            <div x-show="previewUrl" class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px]" style="display: none;">
                <div class="flex items-center gap-1.5 min-w-0">
                    <span x-show="uploadSource"
                          x-text="uploadSource"
                          class="px-1.5 py-0.5 rounded-md bg-emerald-600 text-white text-[10px] font-bold shrink-0"></span>
                    <div class="truncate font-medium">
                        <span class="font-bold" x-text="fileName"></span>
                        <span class="text-emerald-600 ml-1" x-text="'(' + fileSizeText + ' → otomatis dikompres ≤ 100 KB)'"></span>
                    </div>
                </div>
                <button type="button" @click="clearSelected()" class="text-emerald-700 hover:text-rose-600 font-bold shrink-0 px-1.5 cursor-pointer">
                    Batal
                </button>
            </div>

            <p class="text-[11px] text-slate-500 leading-relaxed">
                {{ $hint }}
            </p>
        </div>
    </div>

    <!-- Modal Kamera Live Webcam Modern -->
    <div x-show="cameraOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm" style="display: none;">
        <div @click.away="closeCameraModal()" class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200">
            <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h4 class="text-sm font-bold">Kamera Pengambilan Foto Barang</h4>
                </div>
                <button type="button" @click="closeCameraModal()" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 bg-black flex items-center justify-center">
                <video x-ref="videoPreview" autoplay playsinline class="w-full max-h-80 rounded-2xl object-contain"></video>
                <canvas x-ref="captureCanvas" class="hidden"></canvas>
            </div>
            <div class="p-4 bg-slate-50 flex items-center justify-between gap-3">
                <button type="button" @click="closeCameraModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="button" @click="captureFromWebcam()" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    <span>Jepret Foto</span>
                </button>
            </div>
        </div>
    </div>
</div>
