<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Menampilkan daftar master seluruh pengguna, filter, dan ringkasan metrik.
     */
    public function index(Request $request): View
    {
        // 1. Hitung Ringkasan Statistik
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'pegawai' => User::where('role', 'pegawai')->count(),
            'pimpinan' => User::where('role', 'pimpinan')->count(),
            'aktif' => User::where('is_active', true)->count(),
            'nonaktif' => User::where('is_active', false)->count(),
        ];

        // 2. Query Pengguna dengan Pencarian & Filter
        $query = User::with('ruangan')->latest();

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('nip', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('jabatan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->input('ruangan_id'));
        }

        if ($request->filled('status')) {
            $isActive = $request->input('status') === '1';
            $query->where('is_active', $isActive);
        }

        $users = $query->paginate(15)->withQueryString();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        return view('admin.users.index', compact('users', 'ruangans', 'stats'));
    }

    /**
     * Menampilkan form penambahan pengguna baru.
     */
    public function create(): View
    {
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        return view('admin.users.create', compact('ruangans'));
    }

    /**
     * Menyimpan data pengguna baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:users,nip'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,pegawai,pimpinan'],
            'ruangan_id' => ['nullable', 'exists:ruangan,id'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'golongan' => ['nullable', 'string', 'max:50'],
            'status_pegawai' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di sistem SIPABIS.',
            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',
            'role.required' => 'Role hak akses wajib dipilih.',
            'status_pegawai.required' => 'Status kepegawaian wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        // Default password adalah NIP jika tidak diisi
        $rawPassword = $validated['password'] ?? $validated['nip'];
        $validated['password'] = Hash::make($rawPassword);
        $validated['is_active'] = true;

        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "Pegawai {$user->name} (NIP: {$user->nip}) berhasil ditambahkan ke sistem.");
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(User $user): View
    {
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        return view('admin.users.edit', compact('user', 'ruangans'));
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,pegawai,pimpinan'],
            'ruangan_id' => ['nullable', 'exists:ruangan,id'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'golongan' => ['nullable', 'string', 'max:50'],
            'status_pegawai' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di sistem SIPABIS.',
            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',
        ]);

        // Proteksi agar Admin yang sedang login tidak dapat mencabut hak akses admin dirinya sendiri
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'Anda tidak dapat mencabut hak akses Admin dari akun Anda sendiri yang sedang digunakan.',
            ])->withInput();
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pegawai {$user->name} berhasil diperbarui.");
    }

    /**
     * Mengubah status aktif/nonaktif pengguna (Aktivasi / Deaktivasi Akun).
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Proteksi: Tidak dapat menonaktifkan akun sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.',
            ]);
        }

        $newStatus = ! $user->is_active;
        $user->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('success', "Akun pegawai {$user->name} berhasil {$statusText}.");
    }

    /**
     * Mereset kata sandi pengguna kembali ke NIP resminya.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make($user->nip),
        ]);

        return back()->with('success', "Kata sandi akun {$user->name} berhasil di-reset kembali ke NIP resminya ({$user->nip}).");
    }

    /**
     * Menghapus akun pengguna dari sistem dengan proteksi audit data.
     */
    public function destroy(User $user): RedirectResponse
    {
        // 1. Proteksi akun sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'error' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        // 2. Proteksi integritas audit data persediaan BMN
        $hasTransactions = $user->permintaan()->exists() 
            || $user->transaksiPersediaan()->exists() 
            || $user->stokOpname()->exists();

        if ($hasTransactions) {
            return back()->withErrors([
                'error' => "Pengguna {$user->name} memiliki riwayat transaksi/pengajuan persediaan resmi. Untuk menjaga integritas audit BMN, silakan nonaktifkan akun ini alih-alih menghapusnya permanen.",
            ]);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pegawai {$name} berhasil dihapus permanen dari sistem.");
    }

    /**
     * Mengekspor direktori seluruh pengguna ke format spreadsheet CSV.
     */
    public function exportExcel(): StreamedResponse
    {
        $filename = 'direktori_pegawai_bps_3209_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            
            // BOM UTF-8 agar karakter Excel tidak rusak
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header Kolom
            fputcsv($handle, [
                'No',
                'NIP',
                'Nama Lengkap',
                'Email',
                'Role Akses',
                'Unit Kerja / Ruangan',
                'Jabatan',
                'Golongan',
                'Status Pegawai',
                'Jenis Kelamin',
                'No. HP/WhatsApp',
                'Status Akun',
                'Terdaftar Pada',
            ]);

            $users = User::with('ruangan')->orderBy('name')->get();

            foreach ($users as $index => $u) {
                fputcsv($handle, [
                    $index + 1,
                    "'" . $u->nip, // Kutip tunggal agar Excel menampilkan 18 digit angka utuh
                    $u->name,
                    $u->email,
                    strtoupper($u->role),
                    $u->ruangan ? $u->ruangan->nama_ruangan : '-',
                    $u->jabatan ?? '-',
                    $u->golongan ?? '-',
                    $u->status_pegawai ?? '-',
                    $u->jenis_kelamin_label,
                    $u->no_hp ?? '-',
                    $u->is_active ? 'AKTIF' : 'NONAKTIF',
                    $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
