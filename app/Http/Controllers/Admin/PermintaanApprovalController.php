<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Ruangan;
use App\Services\PermintaanApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermintaanApprovalController extends Controller
{
    public function __construct(
        protected PermintaanApprovalService $approvalService
    ) {}

    /**
     * Menampilkan daftar antrean verifikasi permohonan ATK dari pegawai.
     */
    public function index(Request $request): View
    {
        $query = Permintaan::with(['user', 'ruangan', 'details.barang'])
            ->latest('tanggal_pengajuan')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_permintaan', 'like', "%{$search}%")
                  ->orWhere('catatan_pemohon', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                  });
            });
        }

        $permintaanList = $query->paginate(15)->withQueryString();

        $countDiajukan = Permintaan::where('status', 'diajukan')->count();
        $countDisetujui = Permintaan::where('status', 'disetujui')->count();
        $countSelesai = Permintaan::where('status', 'selesai')->count();
        $countDitolak = Permintaan::where('status', 'ditolak')->count();

        $ruangans = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        return view('admin.permintaan.index', compact(
            'permintaanList',
            'countDiajukan',
            'countDisetujui',
            'countSelesai',
            'countDitolak',
            'ruangans'
        ));
    }

    /**
     * Menampilkan lembar telaah verifikasi permohonan ATK.
     */
    public function show(int $id): View
    {
        $permintaan = Permintaan::with([
            'details.barang.kategori',
            'ruangan',
            'user',
            'verifikator',
            'transaksi'
        ])->findOrFail($id);

        return view('admin.permintaan.show', compact('permintaan'));
    }

    /**
     * Memproses verifikasi persetujuan kuota permohonan ATK (penuh atau sebagian).
     */
    public function verifikasi(Request $request, int $id): RedirectResponse
    {
        $permintaan = Permintaan::with('details')->findOrFail($id);

        $validated = $request->validate([
            'nomor_permintaan_custom' => 'nullable|string|max:100',
            'catatan_admin' => 'nullable|string|max:500',
            'items' => 'required|array',
            'items.*' => 'required|integer|min:0',
        ]);

        if (!empty($validated['nomor_permintaan_custom']) && trim($validated['nomor_permintaan_custom']) !== $permintaan->nomor_permintaan) {
            $permintaan->update([
                'nomor_permintaan' => trim($validated['nomor_permintaan_custom']),
            ]);
        }

        $this->approvalService->verifikasi(
            $permintaan,
            Auth::user(),
            $validated['items'],
            $validated['catatan_admin'] ?? null
        );

        return redirect()->route('admin.permintaan.show', $permintaan->id)
            ->with('success', "Permohonan {$permintaan->nomor_permintaan} berhasil diverifikasi.");
    }

    /**
     * Memproses penolakan pengajuan permohonan ATK dengan alasan resmi.
     */
    public function tolak(Request $request, int $id): RedirectResponse
    {
        $permintaan = Permintaan::findOrFail($id);

        $validated = $request->validate([
            'alasan' => 'required|string|max:500',
        ], [
            'alasan.required' => 'Wajib mengisi alasan penolakan permohonan ATK.',
        ]);

        $this->approvalService->tolak(
            $permintaan,
            Auth::user(),
            $validated['alasan']
        );

        return redirect()->route('admin.permintaan.show', $permintaan->id)
            ->with('success', "Permohonan {$permintaan->nomor_permintaan} telah ditolak.");
    }

    /**
     * Melakukan serah terima fisik barang persediaan kepada pemohon.
     * Mengurangi stok fisik gudang secara atomik.
     */
    public function serahTerima(Request $request, int $id): RedirectResponse
    {
        $permintaan = Permintaan::with(['details.barang'])->findOrFail($id);

        try {
            $this->approvalService->serahTerima($permintaan, Auth::user());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses serah terima fisik: ' . $e->getMessage());
        }

        return redirect()->route('admin.permintaan.show', $permintaan->id)
            ->with('success', "Serah terima fisik berhasil dikonfirmasi. Stok barang telah resmi dipotong dari saldo gudang persediaan BPS.");
    }
}
