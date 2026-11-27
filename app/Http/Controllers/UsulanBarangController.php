<?php

namespace App\Http\Controllers;

use App\Models\UsulanBarang;
use App\Services\ImageOptimizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class UsulanBarangController extends Controller
{
    /**
     * Tampilkan daftar usulan pengadaan barang baru.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = UsulanBarang::with(['user', 'ruangan'])->latest();

        // Jika pegawai, hanya lihat usulan dari unit kerjanya atau miliknya
        if ($user->isPegawai()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->ruangan_id) {
                    $q->orWhere('ruangan_id', $user->ruangan_id);
                }
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Pencarian nama barang usulan
        if ($search = $request->input('search')) {
            $query->where('nama_barang_usulan', 'like', "%{$search}%");
        }

        $usulanList = $query->paginate(15)->withQueryString();

        // Statistik Metrik
        $statsQuery = UsulanBarang::query();
        if ($user->isPegawai()) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->ruangan_id) {
                    $q->orWhere('ruangan_id', $user->ruangan_id);
                }
            });
        }

        $totalUsulan = (clone $statsQuery)->count();
        $totalDiajukan = (clone $statsQuery)->where('status', 'diajukan')->count();
        $totalDipertimbangkan = (clone $statsQuery)->where('status', 'dipertimbangkan')->count();
        $totalDirealisasikan = (clone $statsQuery)->where('status', 'direalisasikan')->count();
        $totalDitolak = (clone $statsQuery)->where('status', 'ditolak')->count();

        return view('usulan-barang.index', compact(
            'usulanList',
            'totalUsulan',
            'totalDiajukan',
            'totalDipertimbangkan',
            'totalDirealisasikan',
            'totalDitolak',
            'status',
            'search'
        ));
    }

    /**
     * Form pengajuan usulan pengadaan barang baru.
     */
    public function create(): View
    {
        return view('usulan-barang.create');
    }

    /**
     * Simpan usulan barang baru ke basis data.
     */
    public function store(Request $request, ImageOptimizationService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'nama_barang_usulan' => 'required|string|max:200',
            'perkiraan_satuan' => 'required|string|max:50',
            'estimasi_kebutuhan' => 'required|integer|min:1',
            'alasan_kebutuhan' => 'required|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'foto.image' => 'Berkas contoh barang harus berupa gambar.',
            'foto.mimes' => 'Format foto barang usulan harus JPG, JPEG, PNG, atau WebP.',
            'foto.max' => 'Ukuran awal foto tidak boleh melebihi 10 MB.',
        ]);

        $user = Auth::user();
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            try {
                $fotoPath = $imageService->simpanFotoUsulan($request->file('foto'));
            } catch (RuntimeException $e) {
                return back()->withInput()->withErrors(['foto' => $e->getMessage()]);
            }
        }

        UsulanBarang::create([
            'user_id' => $user->id,
            'ruangan_id' => $user->ruangan_id ?? 1,
            'nama_barang_usulan' => $validated['nama_barang_usulan'],
            'perkiraan_satuan' => $validated['perkiraan_satuan'],
            'estimasi_kebutuhan' => $validated['estimasi_kebutuhan'],
            'alasan_kebutuhan' => $validated['alasan_kebutuhan'],
            'foto' => $fotoPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('usulan-barang.index')->with('success', 'Usulan pengadaan barang berhasil diajukan ke Subbagian Umum BPS.');
    }

    /**
     * Update status usulan pengadaan barang (Khusus Admin Subbag Umum).
     */
    public function updateStatus(Request $request, UsulanBarang $usulan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:dipertimbangkan,direalisasikan,ditolak',
            'catatan_tindak_lanjut' => 'nullable|string|max:500',
        ]);

        $usulan->update([
            'status' => $validated['status'],
            'catatan_tindak_lanjut' => $validated['catatan_tindak_lanjut'] ?? null,
        ]);

        return back()->with('success', "Status usulan barang '{$usulan->nama_barang_usulan}' berhasil diperbarui menjadi {$usulan->status}.");
    }

    /**
     * Batalkan / Hapus usulan barang.
     */
    public function destroy(UsulanBarang $usulan, ImageOptimizationService $imageService): RedirectResponse
    {
        $user = Auth::user();

        // Hanya pemohon saat masih 'diajukan' atau admin yang boleh menghapus
        if (!$user->isAdmin() && ($usulan->user_id !== $user->id || $usulan->status !== 'diajukan')) {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan usulan ini.');
        }

        $nama = $usulan->nama_barang_usulan;
        $fotoPath = $usulan->foto;
        $usulan->delete();

        $imageService->hapusFoto($fotoPath);

        return redirect()->route('usulan-barang.index')->with('success', "Usulan barang '{$nama}' berhasil dibatalkan.");
    }
}
