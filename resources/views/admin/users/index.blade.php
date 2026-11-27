<x-app-layout>
    <x-slot:title>Manajemen Pengguna - SIPABIS</x-slot:title>
    <x-slot:header>Manajemen Pengguna</x-slot:header>

    <div class="space-y-6">

        <!-- Top Header & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Pengguna</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola data pegawai, peran hak akses, penempatan unit kerja, dan kredensial SIPABIS BPS Kabupaten Cirebon.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.users.export') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 shadow-sm transition hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor CSV</span>
                </a>

                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-brand-lime hover:bg-brand-lime-hover text-slate-950 shadow-sm transition hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Pegawai Baru</span>
                </a>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>Terjadi Kendala:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- KPI Statistic Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Total Pengguna -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Pengguna</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($stats['total']) }}</span>
                    <span class="text-[11px] font-bold text-slate-400">Akun</span>
                </div>
            </div>

            <!-- Admin Persediaan -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-bps-orange-600">Admin</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl sm:text-3xl font-black text-bps-orange-600">{{ number_format($stats['admin']) }}</span>
                    <span class="text-[11px] font-bold text-slate-400">Pengelola</span>
                </div>
            </div>

            <!-- Pegawai Pemohon -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600">Pegawai</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl sm:text-3xl font-black text-teal-600">{{ number_format($stats['pegawai']) }}</span>
                    <span class="text-[11px] font-bold text-slate-400">Pemohon</span>
                </div>
            </div>

            <!-- Pimpinan Satker -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Pimpinan</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl sm:text-3xl font-black text-indigo-600">{{ number_format($stats['pimpinan']) }}</span>
                    <span class="text-[11px] font-bold text-slate-400">Pengesah</span>
                </div>
            </div>

            <!-- Status Aktif / Nonaktif -->
            <div class="col-span-2 sm:col-span-1 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Status Akun</span>
                <div class="mt-2 flex items-center justify-between text-xs font-bold">
                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $stats['aktif'] }} Aktif</span>
                    <span class="text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">{{ $stats['nonaktif'] }} Nonaktif</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                
                <!-- Keyword Search -->
                <div class="lg:col-span-4">
                    <label for="q" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Cari Pegawai</label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               id="q" 
                               value="{{ request('q') }}" 
                               placeholder="Nama, NIP 18 digit, atau email..." 
                               class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="lg:col-span-2">
                    <label for="role" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Role Akses</label>
                    <select name="role" id="role" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Persediaan</option>
                        <option value="pegawai" {{ request('role') === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="pimpinan" {{ request('role') === 'pimpinan' ? 'selected' : '' }}>Pimpinan Satker</option>
                    </select>
                </div>

                <!-- Ruangan / Unit Kerja Filter -->
                <div class="lg:col-span-3">
                    <label for="ruangan_id" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Unit Kerja / Ruangan</label>
                    <select name="ruangan_id" id="ruangan_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        <option value="">Semua Unit Kerja</option>
                        @foreach($ruangans as $ruangan)
                            <option value="{{ $ruangan->id }}" {{ request('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                {{ $ruangan->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="lg:col-span-2">
                    <label for="status" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Status Akun</label>
                    <select name="status" id="status" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Action Button -->
                <div class="lg:col-span-1 flex items-center gap-1.5">
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'role', 'ruangan_id', 'status']))
                        <a href="{{ route('admin.users.index') }}" title="Reset Filter" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Master Users Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" x-data="{ resetModalOpen: false, deleteModalOpen: false, selectedUser: null, resetActionUrl: '', deleteActionUrl: '' }">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Pegawai / NIP</th>
                            <th class="py-3.5 px-4">Role Akses</th>
                            <th class="py-3.5 px-4">Unit Kerja (Buku Kendali)</th>
                            <th class="py-3.5 px-4">Jabatan & Golongan</th>
                            <th class="py-3.5 px-4">Status Akun</th>
                            <th class="py-3.5 px-4 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            <tr class="hover:bg-slate-50/60 transition {{ ! $u->is_active ? 'bg-slate-50/30 opacity-75' : '' }}">
                                <!-- User Info & Avatar -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 shadow-sm">
                                            <img src="{{ $u->foto_url }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-slate-900 truncate">{{ $u->name }}</span>
                                                @if($u->id === auth()->id())
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-indigo-100 text-indigo-700">Anda</span>
                                                @endif
                                            </div>
                                            <p class="font-mono text-[11px] text-slate-500 mt-0.5 tracking-tight">{{ $u->nip }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Badge -->
                                <td class="py-3.5 px-4">
                                    @if($u->isAdmin())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-bps-orange-50 border border-bps-orange-200 text-bps-orange-700">
                                            <svg class="w-3 h-3 text-bps-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span>Admin</span>
                                        </span>
                                    @elseif($u->isPimpinan())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-50 border border-indigo-200 text-indigo-700">
                                            <svg class="w-3 h-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                                            <span>Pimpinan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-teal-50 border border-teal-200 text-teal-700">
                                            <svg class="w-3 h-3 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                            <span>Pegawai</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Ruangan / Unit Kerja -->
                                <td class="py-3.5 px-4">
                                    @if($u->ruangan)
                                        <span class="font-bold text-slate-800">{{ $u->ruangan->nama_ruangan }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum Dipetakan</span>
                                    @endif
                                </td>

                                <!-- Jabatan & Golongan -->
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-medium text-slate-900">{{ $u->jabatan ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-2">
                                        <span>{{ $u->status_pegawai }}</span>
                                        @if($u->golongan)
                                            <span>• Gol. {{ $u->golongan }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status Akun -->
                                <td class="py-3.5 px-4">
                                    @if($u->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 border border-slate-200 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Nonaktif</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi Cepat Buttons -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit Data -->
                                        <a href="{{ route('admin.users.edit', $u) }}" 
                                           class="p-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 hover:text-slate-900 transition" 
                                           title="Ubah Data Pegawai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <!-- Reset Password -->
                                        <button type="button" 
                                                @click="selectedUser = '{{ $u->name }}'; resetActionUrl = '{{ route('admin.users.reset-password', $u) }}'; resetModalOpen = true"
                                                class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 transition" 
                                                title="Reset Sandi ke NIP">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        </button>

                                        <!-- Toggle Status Aktif/Nonaktif -->
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-status', $u) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        data-confirm="Apakah Anda yakin ingin {{ $u->is_active ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $u->name }}? {{ $u->is_active ? 'Pengguna tidak akan dapat login sampai diaktifkan kembali.' : 'Pengguna akan kembali memiliki hak akses ke dalam sistem.' }}"
                                                        data-confirm-title="{{ $u->is_active ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Kembali Akun' }}"
                                                        data-confirm-type="{{ $u->is_active ? 'danger' : 'success' }}"
                                                        data-confirm-btn="{{ $u->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                        class="p-1.5 rounded-lg {{ $u->is_active ? 'bg-slate-100 hover:bg-slate-200 text-slate-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }} transition" 
                                                        title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                    @if($u->is_active)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endif
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Delete Account (With Confirmation Modal) -->
                                        @if($u->id !== auth()->id())
                                            <button type="button" 
                                                    @click="selectedUser = '{{ $u->name }}'; deleteActionUrl = '{{ route('admin.users.destroy', $u) }}'; deleteModalOpen = true"
                                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 transition" 
                                                    title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-600">Tidak ada data pegawai yang sesuai.</p>
                                    <p class="text-xs mt-1">Coba sesuaikan kata kunci pencarian atau reset filter di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif

            <!-- Modal Konfirmasi Reset Password -->
            <div x-show="resetModalOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" 
                 style="display: none;">
                <div @click.away="resetModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Konfirmasi Reset Kata Sandi</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Kata sandi untuk <strong class="text-slate-900" x-text="selectedUser"></strong> akan di-reset kembali sama persis dengan <strong>Nomor Induk Pegawai (NIP)</strong> resminya.
                        </p>
                    </div>
                    <form :action="resetActionUrl" method="POST" class="flex items-center justify-end gap-2.5 pt-2">
                        @csrf
                        <button type="button" @click="resetModalOpen = false" class="px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-amber-500 hover:bg-amber-600 text-white shadow-sm transition">
                            Ya, Reset Sandi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Akun -->
            <div x-show="deleteModalOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" 
                 style="display: none;">
                <div @click.away="deleteModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Konfirmasi Hapus Akun</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus akun <strong class="text-slate-900" x-text="selectedUser"></strong>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                        </p>
                    </div>
                    <form :action="deleteActionUrl" method="POST" class="flex items-center justify-end gap-2.5 pt-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition">
                            Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
