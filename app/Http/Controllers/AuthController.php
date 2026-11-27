<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login BPS.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses otentikasi masuk pengguna berdasarkan NIP saja.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nip.required' => 'NIP (Nomor Induk Pegawai) wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('nip')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withErrors([
                    'nip' => "Terlalu banyak percobaan login yang gagal. Silakan tunggu {$seconds} detik lagi.",
                ])
                ->onlyInput('nip');
        }

        $credentials = [
            'nip' => $request->input('nip'),
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        $user = User::where('nip', $request->input('nip'))->first();

        if ($user && ! $user->is_active && Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors([
                    'nip' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator Subbag Umum BPS Kabupaten Cirebon.',
                ])
                ->onlyInput('nip');
        }

        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withErrors([
                'nip' => 'NIP atau kata sandi yang Anda masukkan tidak sesuai dengan database BPS.',
            ])
            ->onlyInput('nip');
    }

    /**
     * Menampilkan halaman status keluar (logout) atau konfirmasi keluar.
     */
    public function showLogoutPage(): View
    {
        return view('auth.logout');
    }

    /**
     * Menangani proses keluar (logout) dari sistem.
     */
    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
        }

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('logout.page')->with('logged_out', true);
    }
}
