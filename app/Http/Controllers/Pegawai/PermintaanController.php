<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Permintaan;
use App\Services\PermintaanApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermintaanController extends Controller
{
    public function __construct(
        protected PermintaanApprovalService $approvalService
    ) {}

    /**
     * Menampilkan daftar permohonan ATK milik pegawai yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Permintaan::where('user_id', $user->id)
            ->with(['details.barang.kategori', 'ruangan'])
            ->latest('tanggal_pengajuan')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_id')) {
            $query->whereHas('details.barang', function ($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_permintaan', 'like', "%{$search}%")
                    ->orWhere('catatan_pemohon', 'like', "%{$search}%")
                    ->orWhereHas('details.barang', function ($qb) use ($search) {
                        $qb->where('nama_barang', 'like', "%{$search}%")
                            ->orWhere('kode_barang', 'like', "%{$search}%");
                    });
            });
        }

        $permintaanList = $query->paginate(10)->withQueryString();

        $kategoriList = KategoriBarang::orderBy('nama_kategori', 'asc')->get();

        $countDiajukan = Permintaan::where('user_id', $user->id)->where('status', 'diajukan')->count();
        $countDisetujui = Permintaan::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $countSelesai = Permintaan::where('user_id', $user->id)->where('status', 'selesai')->count();

        return view('pegawai.permintaan.index', compact(
            'permintaanList',
            'kategoriList',
            'countDiajukan',
            'countDisetujui',
            'countSelesai'
        ));
    }

    /**
     * Menampilkan form katalog pembuatan permohonan ATK baru.
     */
    public function create(): View
    {
        $barangs = BarangPersediaan::with('kategori')
            ->where('stok_aktif', '>', 0)
            ->orderByRaw('CASE WHEN foto IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('nama_barang', 'asc')
            ->get();

        $kategoriList = KategoriBarang::whereHas('barang', function ($q) {
            $q->where('stok_aktif', '>', 0);
        })
            ->withCount(['barang as aktif_count' => function ($q) {
                $q->where('stok_aktif', '>', 0);
            }])
            ->orderBy('nama_kategori', 'asc')
            ->get();

        $user = Auth::user();

        return view('pegawai.permintaan.create', compact('barangs', 'kategoriList', 'user'));
    }

    /**
     * Menyimpan permohonan ATK baru melalui PermintaanApprovalService.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_pemohon' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang_persediaan,id',
            'items.*.jumlah_diminta' => 'required|integer|min:1',
            'items.*.catatan_item' => 'nullable|string|max:200',
        ], [
            'items.required' => 'Pilih minimal satu barang untuk diajukan.',
            'items.*.barang_id.required' => 'Item barang persediaan wajib dipilih.',
            'items.*.jumlah_diminta.min' => 'Jumlah barang diminta minimal 1 unit.',
        ]);

        $user = Auth::user();

        $permintaan = $this->approvalService->buatPermintaan(
            $user,
            $validated['items'],
            $validated['catatan_pemohon'] ?? null
        );

        return redirect()->route('pegawai.permintaan.index')
            ->with('success', "Permohonan ATK nomor {$permintaan->nomor_permintaan} berhasil diajukan dan sedang menunggu verifikasi Subbag Umum.");
    }

    /**
     * Menampilkan rincian status tiket permohonan ATK.
     */
    public function show(int $id): View
    {
        $user = Auth::user();
        $permintaan = Permintaan::with(['details.barang.kategori', 'ruangan', 'user', 'verifikator'])->findOrFail($id);

        if ($permintaan->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak berhak melihat permohonan pegawai lain.');
        }

        return view('pegawai.permintaan.show', compact('permintaan'));
    }

    /**
     * Cetak formulir resmi permintaan barang persediaan standar BPS (PSD-133).
     */
    public function cetak(int $id): View
    {
        $user = Auth::user();
        $permintaan = Permintaan::with(['details.barang.kategori', 'ruangan', 'user', 'verifikator'])->findOrFail($id);

        if ($user->isPegawai() && $permintaan->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mencetak formulir permohonan pegawai lain.');
        }

        return view('pegawai.permintaan.cetak', compact('permintaan'));
    }

    /**
     * Membatalkan permohonan ATK oleh pemohon.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $permintaan = Permintaan::findOrFail($id);

        if ($permintaan->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak membatalkan permohonan milik pegawai lain.');
        }

        if ($permintaan->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Hanya permohonan yang masih berstatus diajukan yang dapat dibatalkan.');
        }

        $this->approvalService->batalkan(
            $permintaan,
            $user,
            $request->input('alasan')
        );

        return redirect()->route('pegawai.permintaan.index')
            ->with('success', "Permohonan {$permintaan->nomor_permintaan} berhasil dibatalkan.");
    }
}
