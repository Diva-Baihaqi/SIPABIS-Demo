<x-app-layout>
    <x-slot:title>Profil Saya - SIPABIS</x-slot:title>
    <x-slot:header>Profil Pengguna & Keamanan Akun</x-slot:header>

    <div class="space-y-6" x-data="profileManager({
        name: '{{ addslashes($user->name) }}',
        initial: '{{ strtoupper(substr($user->name, 0, 1)) }}',
        nip: '{{ $user->nip }}',
        email: '{{ $user->email }}',
        role: '{{ strtoupper($user->role) }}',
        jabatan: '{{ addslashes($user->jabatan ?? 'Pegawai Organik BPS Kabupaten Cirebon') }}',
        golongan: '{{ $user->golongan ?? '-' }}',
        status_pegawai: '{{ $user->status_pegawai ?? 'PNS' }}',
        jenis_kelamin: '{{ $user->jenis_kelamin }}',
        jenis_kelamin_label: '{{ $user->jenis_kelamin_label }}',
        foto_url: '{{ $user->foto_url }}',
        no_hp: '{{ $user->no_hp ?? '-' }}',
        ruangan_id: '{{ $user->ruangan_id }}',
        ruangan_nama: '{{ addslashes($user->ruangan?->nama_ruangan ?? 'Subbagian Umum') }}',
        ruangan_kode: '{{ $user->ruangan?->kode_ruangan ?? 'TU' }}',
        penanggung_jawab: '{{ addslashes($user->ruangan?->penanggung_jawab ?? '-') }}',
        nip_penanggung_jawab: '{{ $user->ruangan?->nip_penanggung_jawab ?? '-' }}',
        statsCards: @js($activityStats['cards']),
        statsUpdatedAt: '{{ $activityStats['updated_at'] }}',
        statsUrl: '{{ route('profil.stats') }}'
    })">

        <!-- Header Card: Identitas Profil Pengguna -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                    <a href="{{ route('dashboard') }}" class="hover:text-teal-700 transition">Dashboard</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-600">Lainnya</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-900 font-bold">Profil Saya</span>
                </nav>

                <div class="flex items-center gap-4 pt-1">
                    <div class="w-12 h-12 rounded-2xl 
                        @if($user->isAdmin()) bg-amber-50 border border-amber-200 text-amber-700
                        @elseif($user->isPimpinan()) bg-emerald-50 border border-emerald-200 text-emerald-700
                        @else bg-teal-50 border border-teal-200 text-teal-700 @endif 
                        flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Profil Pegawai BPS</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Identitas resmi dan pengaturan keamanan akun SIPABIS</p>
                    </div>
                </div>
            </div>

            <!-- Status Akun Pill Badge -->
            <div class="shrink-0 flex items-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold 
                    @if($user->isAdmin()) bg-amber-50 text-amber-900 border border-amber-200/80
                    @elseif($user->isPimpinan()) bg-emerald-50 text-emerald-900 border border-emerald-200/80
                    @else bg-teal-50 text-teal-900 border border-teal-200/80 @endif shadow-xs">
                    <span class="w-2 h-2 rounded-full 
                        @if($user->isAdmin()) bg-amber-500
                        @elseif($user->isPimpinan()) bg-emerald-500
                        @else bg-teal-600 @endif animate-pulse"></span>
                    <span>Status Akun: Aktif ({{ strtoupper($user->role) }})</span>
                </span>
            </div>
        </div>

        <!-- Floating Toast Notification (Real-Time Update) -->
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-slate-900 text-white border border-slate-700 shadow-2xl"
             style="display: none;">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-white">Pembaruan Real-Time Berhasil</p>
                <p class="text-[11px] text-slate-300" x-text="toastMessage"></p>
            </div>
            <button @click="showToast = false" class="text-slate-400 hover:text-white ml-2 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="p-4 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Column: Official Digital Employee Card (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Digital ID Card Container -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-teal-500/10 via-transparent to-transparent rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Top Card Header: Avatar, Identity & Edit Action -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 pb-6 border-b border-slate-100">
                        <div class="flex items-center gap-5 min-w-0">
                            <div class="w-20 h-20 rounded-3xl overflow-hidden border-2 border-slate-200/90 shadow-md bg-slate-100 flex items-center justify-center shrink-0 relative group">
                                <img :src="profile.foto_url" src="{{ $user->foto_url }}" :alt="profile.name" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 right-1 w-3.5 h-3.5 rounded-full border-2 border-white bg-emerald-500" title="Akun Aktif"></span>
                            </div>

                            <div class="space-y-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 leading-tight" x-text="profile.name">{{ $user->name }}</h2>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if($user->isAdmin()) bg-bps-orange-100 text-bps-orange-800
                                        @elseif($user->isPimpinan()) bg-emerald-100 text-emerald-800
                                        @else bg-teal-100 text-teal-800 @endif"
                                        x-text="profile.role">
                                        {{ $user->role }}
                                    </span>
                                </div>
                                <p class="text-sm font-mono text-slate-600 font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    <span x-text="'NIP: ' + profile.nip">NIP: {{ $user->nip }}</span>
                                </p>
                                <p class="text-xs text-slate-500" x-text="profile.jabatan">{{ $user->jabatan ?? 'Pegawai Organik BPS Kabupaten Cirebon' }}</p>
                            </div>
                        </div>

                        <!-- Edit Button Trigger -->
                        <div class="shrink-0 pt-2 sm:pt-0">
                            <button type="button" @click="openEditModal()"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs hover:shadow transition cursor-pointer">
                                <svg class="w-4 h-4 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Edit Profil</span>
                            </button>
                        </div>
                    </div>

                    <!-- Profile Detailed Attributes Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Satuan Kerja</span>
                            <div class="font-bold text-slate-900 text-sm">428646 — BPS Kabupaten Cirebon</div>
                            <div class="text-[11px] text-slate-500">Provinsi Jawa Barat (3209)</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Unit Kerja / Ruangan</span>
                            <div class="font-bold text-slate-900 text-sm" x-text="profile.ruangan_nama">{{ $user->ruangan->nama_ruangan ?? 'Subbagian Umum' }}</div>
                            <div class="text-[11px] text-slate-500" x-text="'Kode: ' + profile.ruangan_kode">Kode: {{ $user->ruangan->kode_ruangan ?? 'TU' }}</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Golongan / Pangkat</span>
                            <div class="font-bold text-slate-900 text-sm" x-text="'Golongan ' + profile.golongan">Golongan {{ $user->golongan ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500" x-text="'Status: ' + profile.status_pegawai">Status: {{ $user->status_pegawai ?? 'PNS' }}</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Penanggung Jawab Unit Kerja</span>
                            <div class="font-bold text-slate-900 text-sm" x-text="profile.penanggung_jawab">{{ $user->ruangan->penanggung_jawab ?? '-' }}</div>
                            <div class="text-[11px] font-mono text-slate-500" x-text="'NIP. ' + profile.nip_penanggung_jawab">NIP. {{ $user->ruangan->nip_penanggung_jawab ?? '-' }}</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Email Kedinasan</span>
                            <div class="font-bold text-slate-900 text-sm" x-text="profile.email">{{ $user->email }}</div>
                            <div class="text-[11px] text-slate-500">Akun Resmi Domain BPS</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kontak / No. Handphone</span>
                            <div class="font-bold text-slate-900 text-sm" x-text="profile.no_hp">{{ $user->no_hp ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500">Komunikasi Koordinasi Dinas</div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Profil (Alpine.js) -->
                <div x-show="isEditModalOpen" 
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     style="display: none;"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="closeEditModal()"></div>

                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200/90"
                             @click.away="closeEditModal()">
                            
                            <!-- Modal Header -->
                            <div class="px-6 py-5 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-teal-500/20 text-teal-400 border border-teal-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-white">Edit Data Kepegawaian & Profil</h3>
                                        <p class="text-xs text-slate-300">Pembaruan data disimpan dan berubah secara real-time</p>
                                    </div>
                                </div>
                                <button type="button" @click="closeEditModal()" class="text-slate-400 hover:text-white p-1 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <!-- Modal Body Form -->
                            <form @submit.prevent="submitProfile()" class="p-6 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    
                                    <!-- Nama Lengkap & Gelar -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" x-model="form.name" required
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition"
                                               placeholder="Nama lengkap beserta gelar">
                                        <template x-if="errors.name">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.name[0]"></p>
                                        </template>
                                    </div>

                                    <!-- NIP 18 Digit -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            NIP (18 Digit) <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" x-model="form.nip" required maxlength="30"
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:ring-4 transition"
                                               placeholder="Contoh: 197801152002121001">
                                        <template x-if="errors.nip">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.nip[0]"></p>
                                        </template>
                                    </div>

                                    <!-- Jabatan Kedinasan -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Jabatan Kedinasan
                                        </label>
                                        <input type="text" x-model="form.jabatan"
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition"
                                               placeholder="Contoh: Pranata Komputer Ahli Pertama">
                                        <template x-if="errors.jabatan">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.jabatan[0]"></p>
                                        </template>
                                    </div>

                                    <!-- Unit Kerja / Ruangan -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Unit Kerja / Ruangan
                                        </label>
                                        <select x-model="form.ruangan_id"
                                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                            <option value="">-- Pilih Unit Kerja --</option>
                                            @foreach($ruanganList ?? [] as $r)
                                                <option value="{{ $r->id }}">{{ $r->nama_ruangan }} ({{ $r->kode_ruangan }})</option>
                                            @endforeach
                                        </select>
                                        <template x-if="errors.ruangan_id">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.ruangan_id[0]"></p>
                                        </template>
                                    </div>

                                    <!-- Golongan / Pangkat -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Golongan / Pangkat
                                        </label>
                                        <select x-model="form.golongan"
                                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                            <option value="">-- Pilih Golongan --</option>
                                            <optgroup label="Golongan IV (Pembina)">
                                                <option value="IV/e">IV/e (Pembina Utama)</option>
                                                <option value="IV/d">IV/d (Pembina Utama Madya)</option>
                                                <option value="IV/c">IV/c (Pembina Utama Muda)</option>
                                                <option value="IV/b">IV/b (Pembina Tingkat I)</option>
                                                <option value="IV/a">IV/a (Pembina)</option>
                                            </optgroup>
                                            <optgroup label="Golongan III (Penata)">
                                                <option value="III/d">III/d (Penata Tingkat I)</option>
                                                <option value="III/c">III/c (Penata)</option>
                                                <option value="III/b">III/b (Penata Muda Tingkat I)</option>
                                                <option value="III/a">III/a (Penata Muda)</option>
                                            </optgroup>
                                            <optgroup label="Golongan II (Pengatur)">
                                                <option value="II/d">II/d (Pengatur Tingkat I)</option>
                                                <option value="II/c">II/c (Pengatur)</option>
                                                <option value="II/b">II/b (Pengatur Muda Tingkat I)</option>
                                                <option value="II/a">II/a (Pengatur Muda)</option>
                                            </optgroup>
                                        </select>
                                        <template x-if="errors.golongan">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.golongan[0]"></p>
                                        </template>
                                    </div>

                                    <!-- Status Kepegawaian -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Status Kepegawaian
                                        </label>
                                        <select x-model="form.status_pegawai"
                                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                            <option value="PNS">PNS (Pegawai Negeri Sipil)</option>
                                            <option value="PPPK">PPPK (Pegawai Pemerintah dg Perjanjian Kerja)</option>
                                            <option value="PPNPN">PPNPN (Pegawai Pemerintah Non Pegawai Negeri)</option>
                                            <option value="Mitra">Mitra Statistik</option>
                                        </select>
                                        <template x-if="errors.status_pegawai">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.status_pegawai[0]"></p>
                                        </template>
                                    </div>

                                    <!-- Jenis Kelamin / Foto Profil -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Jenis Kelamin / Foto Avatar <span class="text-rose-500">*</span>
                                        </label>
                                        <select x-model="form.jenis_kelamin" required
                                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                            <option value="L">Laki-laki (Avatar Pria)</option>
                                            <option value="P">Perempuan (Avatar Wanita)</option>
                                        </select>
                                        <template x-if="errors.jenis_kelamin">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.jenis_kelamin[0]"></p>
                                        </template>
                                    </div>

                                    <!-- Email Kedinasan -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Email Kedinasan <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="email" x-model="form.email" required
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition"
                                               placeholder="nama@bps.go.id">
                                        <template x-if="errors.email">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.email[0]"></p>
                                        </template>
                                    </div>

                                    <!-- No. Handphone / WhatsApp -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Kontak / No. Handphone
                                        </label>
                                        <input type="text" x-model="form.no_hp"
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition"
                                               placeholder="Contoh: 081234567890">
                                        <template x-if="errors.no_hp">
                                            <p class="text-[11px] text-rose-500 font-medium" x-text="errors.no_hp[0]"></p>
                                        </template>
                                    </div>

                                </div>

                                <!-- Modal Actions -->
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                                    <button type="button" @click="closeEditModal()"
                                            class="px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" :disabled="isSubmitting"
                                            class="px-5 py-2.5 rounded-2xl bg-teal-700 hover:bg-teal-800 disabled:opacity-50 text-white text-xs font-bold shadow-xs hover:shadow transition flex items-center gap-2 cursor-pointer">
                                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- Activity Summary Cards (Real-Time & Role-Aware) -->
                @php
                    $cardTones = [
                        'slate' => [
                            'number' => 'text-slate-900',
                            'icon_bg' => 'bg-slate-100 text-slate-600 group-hover:bg-bps-navy-700 group-hover:text-white',
                            'ring' => 'ring-2 ring-bps-blue-500/40 border-bps-blue-500/50',
                        ],
                        'amber' => [
                            'number' => 'text-amber-600',
                            'icon_bg' => 'bg-amber-50 text-amber-600 group-hover:bg-amber-500 group-hover:text-white',
                            'ring' => 'ring-2 ring-amber-400/50 border-amber-300',
                        ],
                        'emerald' => [
                            'number' => 'text-emerald-600',
                            'icon_bg' => 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white',
                            'ring' => 'ring-2 ring-emerald-400/50 border-emerald-300',
                        ],
                    ];
                @endphp

                <div class="space-y-3">
                    <!-- Real-Time Sync Status Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-2 px-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold text-slate-700">Ringkasan Aktivitas</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[10px] font-bold text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
                                <span>Live</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                            <span class="font-mono tabular-nums" x-text="'Diperbarui ' + statsUpdatedAt">Diperbarui {{ $activityStats['updated_at'] }}</span>
                            <button type="button"
                                    @click="refreshStats(true)"
                                    :disabled="isSyncingStats"
                                    title="Perbarui angka statistik sekarang"
                                    class="p-1 rounded-lg text-slate-400 hover:text-bps-navy-700 hover:bg-slate-200/70 transition cursor-pointer disabled:opacity-50">
                                <svg class="w-3.5 h-3.5" :class="isSyncingStats ? 'animate-spin text-bps-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- 3 Role-Aware Interactive Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($activityStats['cards'] as $cardIdx => $card)
                            @php
                                $tone = $cardTones[$card['tone']] ?? $cardTones['slate'];
                            @endphp
                            <a href="{{ $card['url'] }}"
                               :class="statsPulse[{{ $cardIdx }}] ? '{{ $tone['ring'] }}' : 'border-slate-200/80'"
                               class="group bg-white rounded-3xl p-5 border shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 flex flex-col justify-between space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider leading-snug"
                                          x-text="statsCards[{{ $cardIdx }}]?.label ?? '{{ $card['label'] }}'">{{ $card['label'] }}</span>
                                    <span class="w-7 h-7 rounded-xl {{ $tone['icon_bg'] }} flex items-center justify-center shrink-0 transition-colors">
                                        @if($card['key'] === 'total')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        @elseif($card['key'] === 'aktif')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </span>
                                </div>
                                <div class="text-3xl font-black {{ $tone['number'] }} font-mono tabular-nums tracking-tight"
                                     x-text="statsCards[{{ $cardIdx }}]?.value ?? {{ $card['value'] }}">{{ $card['value'] }}</div>
                                <div class="flex items-center justify-between gap-2 pt-0.5">
                                    <p class="text-[11px] text-slate-500 leading-snug"
                                       x-text="statsCards[{{ $cardIdx }}]?.subtitle ?? '{{ $card['subtitle'] }}'">{{ $card['subtitle'] }}</p>
                                    <span class="text-xs text-slate-300 group-hover:text-bps-navy-700 group-hover:translate-x-0.5 transition-all shrink-0">&rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Right Column: Change Password Card (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-5"
                     x-data="{ showPass1: false, showPass2: false, showPass3: false }">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <h3 class="font-bold text-slate-900 text-base">Ubah Kata Sandi</h3>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Secara default kata sandi adalah NIP 18 digit Anda. Anda dapat mengubahnya ke kata sandi pribadi untuk keamanan ekstra.
                        </p>
                    </div>

                    <!-- Errors Alert -->
                    @if ($errors->any())
                        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profil.update-password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-2xl">
                                <input :type="showPass1 ? 'text' : 'password'"
                                       name="current_password"
                                       id="current_password"
                                       required
                                       placeholder="Masukkan kata sandi saat ini"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                <button type="button" @click="showPass1 = !showPass1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg x-show="!showPass1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPass1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-2xl">
                                <input :type="showPass2 ? 'text' : 'password'"
                                       name="password"
                                       id="password"
                                       required
                                       placeholder="Minimal 6 karakter"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                <button type="button" @click="showPass2 = !showPass2" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg x-show="!showPass2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPass2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-2xl">
                                <input :type="showPass3 ? 'text' : 'password'"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       required
                                       placeholder="Ulangi kata sandi baru"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 rounded-2xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-4 transition">
                                <button type="button" @click="showPass3 = !showPass3" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg x-show="!showPass3" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPass3" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold text-xs tracking-wide shadow-xs transition duration-200 cursor-pointer">
                            Simpan Kata Sandi Baru
                        </button>
                    </form>
                </div>

                <!-- Assistance Card -->
                <div class="p-5 rounded-3xl bg-slate-100 border border-slate-200/80 text-xs space-y-2 text-slate-600">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Butuh Pembaruan Data Pegawai?</span>
                    </div>
                    <p class="leading-relaxed">
                        Jika terdapat kekeliruan nama, gelar, pangkat, atau ruangan kerja, silakan gunakan tombol <strong>Edit Profil</strong> di atas atau koordinasikan dengan Subbag Umum BPS Kabupaten Cirebon.
                    </p>
                </div>

            </div>

        </div>

    </div>

    <!-- Script Real-Time Profile Manager -->
    <script>
        function profileManager(initialData) {
            return {
                profile: { ...initialData },
                form: {
                    name: initialData.name,
                    nip: initialData.nip,
                    email: initialData.email,
                    jabatan: initialData.jabatan === 'Pegawai Organik BPS Kabupaten Cirebon' ? '' : initialData.jabatan,
                    golongan: initialData.golongan === '-' ? '' : initialData.golongan,
                    status_pegawai: initialData.status_pegawai,
                    jenis_kelamin: initialData.jenis_kelamin,
                    ruangan_id: initialData.ruangan_id || '',
                    no_hp: initialData.no_hp === '-' ? '' : initialData.no_hp,
                },
                isEditModalOpen: false,
                isSubmitting: false,
                toastMessage: '',
                showToast: false,
                errors: {},

                // Real-time activity stats state
                statsCards: Array.isArray(initialData.statsCards) ? initialData.statsCards : [],
                statsUpdatedAt: initialData.statsUpdatedAt || '',
                statsUrl: initialData.statsUrl || '{{ route('profil.stats') }}',
                statsPulse: [false, false, false],
                isSyncingStats: false,
                statsTimerId: null,
                visibilityHandler: null,

                init() {
                    this.visibilityHandler = () => {
                        if (!document.hidden) {
                            this.refreshStats(false);
                        }
                    };
                    document.addEventListener('visibilitychange', this.visibilityHandler);
                    this.statsTimerId = setInterval(() => {
                        if (!document.hidden) {
                            this.refreshStats(false);
                        }
                    }, 5000);
                },

                destroy() {
                    if (this.statsTimerId) {
                        clearInterval(this.statsTimerId);
                        this.statsTimerId = null;
                    }
                    if (this.visibilityHandler) {
                        document.removeEventListener('visibilitychange', this.visibilityHandler);
                        this.visibilityHandler = null;
                    }
                },

                async refreshStats(isManual = false) {
                    if (this.isSyncingStats) return;
                    this.isSyncingStats = true;

                    try {
                        const response = await fetch(this.statsUrl, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            cache: 'no-store',
                        });

                        if (response.status === 401 || response.status === 419) {
                            this.destroy();
                            return;
                        }
                        if (!response.ok) return;
                        const data = await response.json();
                        if (!data || !data.success || !Array.isArray(data.cards)) return;

                        data.cards.forEach((nextCard, idx) => {
                            const prevVal = this.statsCards[idx]?.value;
                            if (prevVal !== undefined && prevVal !== nextCard.value) {
                                this.statsPulse[idx] = true;
                                setTimeout(() => {
                                    this.statsPulse[idx] = false;
                                }, 1400);
                            }
                        });

                        this.statsCards = data.cards;
                        if (data.updated_at) {
                            this.statsUpdatedAt = data.updated_at;
                        }
                    } catch (err) {
                        if (isManual) {
                            console.error('Gagal memperbarui statistik profil:', err);
                        }
                    } finally {
                        this.isSyncingStats = false;
                    }
                },

                openEditModal() {
                    this.form.name = this.profile.name;
                    this.form.nip = this.profile.nip;
                    this.form.email = this.profile.email;
                    this.form.jabatan = this.profile.jabatan === 'Pegawai Organik BPS Kabupaten Cirebon' ? '' : this.profile.jabatan;
                    this.form.golongan = this.profile.golongan === '-' ? '' : this.profile.golongan;
                    this.form.status_pegawai = this.profile.status_pegawai;
                    this.form.jenis_kelamin = this.profile.jenis_kelamin;
                    this.form.ruangan_id = this.profile.ruangan_id || '';
                    this.form.no_hp = this.profile.no_hp === '-' ? '' : this.profile.no_hp;
                    this.errors = {};
                    this.isEditModalOpen = true;
                },

                closeEditModal() {
                    this.isEditModalOpen = false;
                    this.errors = {};
                },

                async submitProfile() {
                    this.isSubmitting = true;
                    this.errors = {};

                    try {
                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

                        const response = await fetch('{{ route('profil.update') }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422 && data.errors) {
                                this.errors = data.errors;
                            } else {
                                alert(data.message || 'Terjadi kesalahan saat memperbarui profil.');
                            }
                            this.isSubmitting = false;
                            return;
                        }

                        // Update data reactive secara realtime
                        this.profile = { ...data.user };

                        // Update nama & foto avatar di sidebar secara real-time
                        const sidebarNameEl = document.querySelector('.sidebar-user-name');
                        if (sidebarNameEl) {
                            sidebarNameEl.textContent = data.user.name;
                        }
                        const sidebarAvatarEl = document.querySelector('.sidebar-user-avatar');
                        if (sidebarAvatarEl && data.user.foto_url) {
                            sidebarAvatarEl.src = data.user.foto_url;
                        }

                        // Tutup modal
                        this.isEditModalOpen = false;
                        this.isSubmitting = false;

                        // Tampilkan Toast Sukses Realtime
                        this.toastMessage = data.message || 'Profil berhasil diperbarui secara real-time!';
                        this.showToast = true;
                        setTimeout(() => {
                            this.showToast = false;
                        }, 4000);

                    } catch (err) {
                        console.error('Error submitting profile update:', err);
                        this.isSubmitting = false;
                        alert('Gagal terhubung ke server. Periksa koneksi jaringan Anda.');
                    }
                }
            };
        }

        window.profileManager = profileManager;
        if (window.Alpine) {
            window.Alpine.data('profileManager', profileManager);
        }
    </script>
</x-app-layout>
