<x-app-layout>
    <x-slot:title>Ubah Data Pegawai - SIPABIS</x-slot:title>
    <x-slot:header>Ubah Data Pegawai</x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Breadcrumb & Back Button -->
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.users.index') }}" class="hover:text-slate-800 transition">Manajemen Pengguna</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-xs">Edit: {{ $user->name }}</span>
            </nav>

            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar</span>
            </a>
        </div>

        <!-- Alert Errors -->
        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>Terdapat Kesalahan:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 sm:p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Identitas Kepegawaian BPS -->
                <div>
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 mb-5">
                        <span class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center font-bold text-xs">1</span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Identitas Utama Pegawai</h2>
                            <p class="text-[11px] text-slate-500">Nomor induk resmi, nama lengkap, dan kontak kedinasan BPS.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- NIP -->
                        <div>
                            <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor Induk Pegawai (NIP) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nip" 
                                   id="nip" 
                                   value="{{ old('nip', $user->nip) }}" 
                                   required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Email Kedinasan <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <!-- No. WhatsApp / HP -->
                        <div>
                            <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                No. WhatsApp / HP
                            </label>
                            <input type="text" 
                                   name="no_hp" 
                                   id="no_hp" 
                                   value="{{ old('no_hp', $user->no_hp) }}" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                                    <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'L' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                                    <span>Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                                    <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'P' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                                    <span>Perempuan</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Penempatan Unit Kerja & Peran Sistem -->
                <div>
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 mb-5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-xs">2</span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Peran & Unit Kerja (Buku Kendali)</h2>
                            <p class="text-[11px] text-slate-500">Menentukan hak akses sistem dan alokasi buku kendali persediaan.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Role Hak Akses -->
                        <div>
                            <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Role Hak Akses <span class="text-rose-500">*</span>
                            </label>
                            <select name="role" id="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                <option value="pegawai" {{ old('role', $user->role) === 'pegawai' ? 'selected' : '' }}>Pegawai (Pemohon ATK)</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Pengelola Persediaan)</option>
                                <option value="pimpinan" {{ old('role', $user->role) === 'pimpinan' ? 'selected' : '' }}>Pimpinan (Pengesah BASO)</option>
                            </select>
                            @if($user->id === auth()->id())
                                <p class="text-[10px] text-indigo-600 mt-1">🔒 Anda sedang mengedit akun Anda sendiri. Role Admin tidak dapat dicabut.</p>
                            @endif
                        </div>

                        <!-- Unit Kerja / Ruangan -->
                        <div>
                            <label for="ruangan_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Unit Kerja / Ruangan (Tim Fungsi BPS)
                            </label>
                            <select name="ruangan_id" id="ruangan_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id', $user->ruangan_id) == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }} (Kode: {{ $r->kode_ruangan }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">Mengubah unit kerja akan memindahkan tujuan pencatatan mutasi ATK pegawai ini ke buku kendali yang baru.</p>
                        </div>

                        <!-- Status Pegawai -->
                        <div>
                            <label for="status_pegawai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Status Kepegawaian <span class="text-rose-500">*</span>
                            </label>
                            <select name="status_pegawai" id="status_pegawai" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                                <option value="PNS" {{ old('status_pegawai', $user->status_pegawai) === 'PNS' ? 'selected' : '' }}>PNS (Pegawai Negeri Sipil)</option>
                                <option value="PPPK" {{ old('status_pegawai', $user->status_pegawai) === 'PPPK' ? 'selected' : '' }}>PPPK (Pegawai Pemerintah dg Perjanjian Kerja)</option>
                                <option value="PPNPN" {{ old('status_pegawai', $user->status_pegawai) === 'PPNPN' ? 'selected' : '' }}>PPNPN (Pegawai Non PNS)</option>
                                <option value="Mitra" {{ old('status_pegawai', $user->status_pegawai) === 'Mitra' ? 'selected' : '' }}>Mitra Statistik</option>
                            </select>
                        </div>

                        <!-- Golongan -->
                        <div>
                            <label for="golongan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Golongan / Ruang
                            </label>
                            <input type="text" 
                                   name="golongan" 
                                   id="golongan" 
                                   value="{{ old('golongan', $user->golongan) }}" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>

                        <!-- Jabatan -->
                        <div class="sm:col-span-2">
                            <label for="jabatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jabatan Dinas
                            </label>
                            <input type="text" 
                                   name="jabatan" 
                                   id="jabatan" 
                                   value="{{ old('jabatan', $user->jabatan) }}" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pengubahan Kata Sandi (Opsional) -->
                <div>
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 mb-5">
                        <span class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold text-xs">3</span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Perbarui Kata Sandi (Opsional)</h2>
                            <p class="text-[11px] text-slate-500">Biarkan kolom kosong jika tidak ingin mengubah kata sandi pengguna saat ini.</p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               placeholder="Kosongkan jika tidak ingin mengubah sandi" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition hover:scale-105 active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Perbarui Data Pegawai</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
