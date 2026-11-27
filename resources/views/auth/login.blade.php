<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Sistem | SIPABIS BPS Kabupaten Cirebon</title>

    <!-- Google Fonts: DM Serif Display, Plus Jakarta Sans, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-x-hidden selection:bg-bps-orange-500 selection:text-white"
      x-data="{ showPassword: false }">

    <!-- Fullscreen Photographic Background with Soft Corner Vignette -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <img src="{{ asset('images/bps.jpg') }}" 
             alt="Kantor BPS Kabupaten Cirebon" 
             class="w-full h-full object-cover object-center scale-105 transform">
        <!-- Subtle corner vignette only: 100% transparent in center, gentle soft shadow only at outer corners -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_60%,rgba(15,23,42,0.35)_100%)]"></div>
    </div>

    <!-- Login Container Card (Split Screen with Editorial Polish) -->
    <div class="relative z-10 w-full max-w-4xl bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_25px_60px_-15px_rgba(2,6,23,0.35)] border border-white/50 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">
        
        <!-- Left Side: Editorial Showcase with BPS Photography -->
        <div class="hidden lg:flex lg:col-span-5 relative text-white p-10 flex-col justify-between overflow-hidden bg-slate-900">
            <!-- Background Photography of BPS Office -->
            @if(file_exists(public_path('images/bps.jpg')))
                <img src="{{ asset('images/bps.jpg') }}" alt="Kantor BPS Cirebon" class="absolute inset-0 w-full h-full object-cover object-center opacity-40 scale-105">
            @endif

            <!-- Gradient Mask Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/80 to-slate-900/60"></div>
            <div class="absolute -top-20 -left-20 w-60 h-60 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header -->
            <div class="relative z-10 space-y-4">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                    <x-bps-logo size="md" />
                    <div>
                        <h2 class="font-black text-xl tracking-tight text-white group-hover:text-brand-lime transition">SIPABIS</h2>
                        <p class="text-[10px] text-slate-300 font-bold uppercase tracking-wider">BPS Kabupaten Cirebon</p>
                    </div>
                </a>

                <div class="pt-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] font-semibold text-teal-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-lime animate-pulse"></span>
                        <span>Portal Resmi Persediaan ATK</span>
                    </div>
                </div>
            </div>

            <!-- Middle Quote & Integrity -->
            <div class="relative z-10 my-8 space-y-3">
                <span class="text-xs font-bold tracking-widest uppercase text-brand-lime block">AKUNTABILITAS BMN</span>
                <blockquote class="font-serif text-lg text-white leading-relaxed italic">
                    "Menyediakan data statistik berkualitas, didukung tata kelola administrasi dan persediaan logistik yang tertib dan transparan."
                </blockquote>
                <p class="text-xs text-slate-300 font-medium">— Subbag Umum BPS Kab. Cirebon</p>
            </div>

            <!-- Bottom Institutional Details -->
            <div class="relative z-10 text-[11px] text-slate-400 pt-4 border-t border-white/10 space-y-1">
                <p>Kode Satuan Kerja: <strong class="text-white font-mono">428646</strong></p>
                <p>Wilayah: <strong class="text-white">Kabupaten Cirebon (3209)</strong></p>
            </div>
        </div>

        <!-- Right Side: The Form -->
        <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-between bg-white">
            
            <div>
                <!-- Mobile Logo Header -->
                <div class="lg:hidden flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <x-bps-logo size="sm" />
                        <div>
                            <h2 class="font-bold text-base text-slate-900 leading-tight">SIPABIS</h2>
                            <p class="text-[10px] text-slate-500">BPS Kabupaten Cirebon</p>
                        </div>
                    </a>
                    <a href="{{ url('/') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                        &larr; Beranda
                    </a>
                </div>

                <!-- Form Heading -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <h1 class="font-serif text-2xl sm:text-3xl font-normal text-slate-900 tracking-tight">Masuk ke Sistem</h1>
                        <a href="{{ url('/') }}" class="hidden lg:inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Ke Beranda</span>
                        </a>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Gunakan Nomor Induk Pegawai (NIP) resmi sebagai ID Pengguna dan Kata Sandi.</p>
                </div>

                <!-- Alerts -->
                @if (session('success'))
                    <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>Gagal Masuk:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- NIP Field -->
                    <div>
                        <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Induk Pegawai (NIP)
                        </label>
                        <div class="relative rounded-2xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   value="{{ old('nip') }}"
                                   required
                                   autofocus
                                   placeholder="Contoh: 19890101... atau admin"
                                   class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border @error('nip') border-rose-300 ring-rose-200 @else border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 @enderror rounded-2xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 transition duration-150">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative rounded-2xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   id="password"
                                   required
                                   placeholder="Masukkan kata sandi (NIP)"
                                   class="w-full pl-10 pr-10 py-3 bg-slate-50 border @error('password') border-rose-300 ring-rose-200 @else border-slate-200 focus:border-teal-500 focus:ring-teal-500/20 @enderror rounded-2xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 transition duration-150">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                    aria-label="Tampilkan atau sembunyikan kata sandi">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="remember"
                                   class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500/20">
                            <span class="text-xs font-medium text-slate-600">Ingat Sesi di Perangkat Ini</span>
                        </label>
                    </div>

                    <!-- Submit Button: Soft Lime Pill matching reference design -->
                    <button type="submit"
                            class="w-full mt-3 py-3.5 px-4 bg-brand-lime hover:bg-brand-lime-hover active:scale-[0.99] text-slate-950 font-bold text-sm rounded-full shadow-sm hover:shadow transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk ke Akun</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>

        </div>

    </div>

</body>
</html>