<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Models\StokOpname;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil pengguna dan data kepegawaian resmi BPS.
     */
    public function index(): View
    {
        $user = Auth::user()->load('ruangan');
        $ruanganList = Ruangan::orderBy('nama_ruangan')->get();
        $activityStats = $this->buildActivityStats($user);

        return view('profil.index', compact('user', 'activityStats', 'ruanganList'));
    }

    /**
     * Endpoint JSON real-time untuk memperbarui kartu statistik aktivitas profil.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();
        $activityStats = $this->buildActivityStats($user);

        return response()->json([
            'success' => true,
            'cards' => $activityStats['cards'],
            'updated_at' => $activityStats['updated_at'],
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Memperbarui data profil pengguna secara mandiri (Mendukung Real-Time AJAX).
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'golongan' => ['nullable', 'string', 'max:50'],
            'status_pegawai' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'ruangan_id' => ['nullable', 'exists:ruangan,id'],
            'no_hp' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar pada pengguna lain.',
            'email.required' => 'Email kedinasan wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar pada pengguna lain.',
            'ruangan_id.exists' => 'Unit kerja/ruangan yang dipilih tidak valid.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'jabatan' => $validated['jabatan'] ?? null,
            'golongan' => $validated['golongan'] ?? null,
            'status_pegawai' => $validated['status_pegawai'] ?? 'PNS',
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? ($user->jenis_kelamin ?: ($user->isPerempuan() ? 'P' : 'L')),
            'ruangan_id' => $validated['ruangan_id'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
        ]);

        $user->load('ruangan');

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil Anda berhasil diperbarui secara real-time.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initial' => strtoupper(substr($user->name, 0, 1)),
                    'nip' => $user->nip,
                    'email' => $user->email,
                    'role' => strtoupper($user->role),
                    'jabatan' => $user->jabatan ?? 'Pegawai Organik BPS Kabupaten Cirebon',
                    'golongan' => $user->golongan ?? '-',
                    'status_pegawai' => $user->status_pegawai ?? 'PNS',
                    'jenis_kelamin' => $user->jenis_kelamin,
                    'jenis_kelamin_label' => $user->jenis_kelamin_label,
                    'foto_url' => $user->foto_url,
                    'no_hp' => $user->no_hp ?? '-',
                    'ruangan_id' => $user->ruangan_id,
                    'ruangan_nama' => $user->ruangan?->nama_ruangan ?? 'Subbagian Umum',
                    'ruangan_kode' => $user->ruangan?->kode_ruangan ?? 'TU',
                    'penanggung_jawab' => $user->ruangan?->penanggung_jawab ?? '-',
                    'nip_penanggung_jawab' => $user->ruangan?->nip_penanggung_jawab ?? '-',
                ],
            ]);
        }

        return redirect()->route('profil.index')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Memperbarui kata sandi akun pengguna secara mandiri.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok dengan data sistem.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profil.index')
            ->with('success', 'Kata sandi akun Anda berhasil diperbarui.');
    }

    /**
     * Menghitung 3 kartu statistik aktivitas sesuai peran pengguna secara akurat.
     */
    private function buildActivityStats(User $user): array
    {
        if ($user->isAdmin()) {
            $cards = [
                [
                    'key' => 'total',
                    'label' => 'Total Permintaan Kantor',
                    'value' => Permintaan::count(),
                    'subtitle' => 'Seluruh tiket ATK masuk',
                    'tone' => 'slate',
                    'url' => route('admin.permintaan.index'),
                ],
                [
                    'key' => 'aktif',
                    'label' => 'Permintaan Aktif',
                    'value' => Permintaan::whereIn('status', ['diajukan', 'disetujui'])->count(),
                    'subtitle' => 'Perlu verifikasi / serah terima',
                    'tone' => 'amber',
                    'url' => route('admin.permintaan.index', ['status' => 'diajukan']),
                ],
                [
                    'key' => 'selesai',
                    'label' => 'Permintaan Selesai',
                    'value' => Permintaan::where('status', 'selesai')->count(),
                    'subtitle' => 'Telah diserahterimakan',
                    'tone' => 'emerald',
                    'url' => route('admin.permintaan.index', ['status' => 'selesai']),
                ],
            ];
        } elseif ($user->isPimpinan()) {
            $cards = [
                [
                    'key' => 'total',
                    'label' => 'Total Permintaan Kantor',
                    'value' => Permintaan::count(),
                    'subtitle' => 'Seluruh tiket ATK satker',
                    'tone' => 'slate',
                    'url' => route('kartu-kendali.index'),
                ],
                [
                    'key' => 'aktif',
                    'label' => 'BASO Menunggu Pengesahan',
                    'value' => StokOpname::where('status', 'final')->count(),
                    'subtitle' => 'Siap ditinjau & disahkan',
                    'tone' => 'amber',
                    'url' => route('stok-opname.index'),
                ],
                [
                    'key' => 'selesai',
                    'label' => 'BASO Telah Disahkan',
                    'value' => StokOpname::where('status', 'disahkan')->count(),
                    'subtitle' => 'Dokumen resmi disahkan',
                    'tone' => 'emerald',
                    'url' => route('stok-opname.index'),
                ],
            ];
        } else {
            $cards = [
                [
                    'key' => 'total',
                    'label' => 'Total Permintaan Saya',
                    'value' => Permintaan::where('user_id', $user->id)->count(),
                    'subtitle' => 'Tiket pernah Anda ajukan',
                    'tone' => 'slate',
                    'url' => route('pegawai.permintaan.index'),
                ],
                [
                    'key' => 'aktif',
                    'label' => 'Permintaan Aktif',
                    'value' => Permintaan::where('user_id', $user->id)->whereIn('status', ['diajukan', 'disetujui'])->count(),
                    'subtitle' => 'Menunggu verifikasi / siap diambil',
                    'tone' => 'amber',
                    'url' => route('pegawai.permintaan.index'),
                ],
                [
                    'key' => 'selesai',
                    'label' => 'Permintaan Selesai',
                    'value' => Permintaan::where('user_id', $user->id)->where('status', 'selesai')->count(),
                    'subtitle' => 'Barang telah diterima',
                    'tone' => 'emerald',
                    'url' => route('pegawai.permintaan.index'),
                ],
            ];
        }

        return [
            'cards' => $cards,
            'updated_at' => Carbon::now('Asia/Jakarta')->format('H:i:s') . ' WIB',
        ];
    }
}
