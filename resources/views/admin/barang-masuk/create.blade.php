<x-app-layout>
    <x-slot:title>Input Penerimaan Barang Masuk</x-slot:title>
    <x-slot:header>Catat Penerimaan Barang Masuk (Pengadaan)</x-slot:header>

    <div class="max-w-5xl mx-auto space-y-6" 
         x-data="{
             items: [
                 { barang_id: '', jumlah: 1, satuan: '-', stok: 0 }
             ],
             barangs: {{ Js::from($barangs->map(fn($b) => ['id' => $b->id, 'nama' => $b->nama_barang, 'kode' => $b->kode_barang, 'satuan' => $b->satuan, 'stok' => $b->stok_aktif])) }},
             
             addItem() {
                 this.items.push({ barang_id: '', jumlah: 1, satuan: '-', stok: 0 });
             },
             
             removeItem(index) {
                 if (this.items.length > 1) {
                     this.items.splice(index, 1);
                 }
             },
             
             updateItemMeta(index) {
                 const selected = this.barangs.find(b => b.id == this.items[index].barang_id);
                 if (selected) {
                     this.items[index].satuan = selected.satuan;
                     this.items[index].stok = selected.stok;
                 } else {
                     this.items[index].satuan = '-';
                     this.items[index].stok = 0;
                 }
             }
         }">

        <!-- Back Button & Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.barang-masuk.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Riwayat Barang Masuk</span>
            </a>
            <span class="text-[11px] font-mono text-slate-400">Form Pengadaan ATK DIPA BPS 3209</span>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="font-bold mb-1">Periksa kembali data yang dimasukkan:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.barang-masuk.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Dokumen & Metadata Card -->
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-base font-extrabold text-slate-900">1. Informasi Dokumen & Rekanan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Informasi surat jalan, faktur, atau kwitansi penerimaan dari pihak ketiga.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="tanggal_transaksi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tanggal Penerimaan Fisik <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               id="tanggal_transaksi" 
                               name="tanggal_transaksi" 
                               value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" 
                               required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label for="nomor_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nomor Dokumen / Surat Jalan / Kwitansi
                        </label>
                        <input type="text" 
                               id="nomor_dokumen" 
                               name="nomor_dokumen" 
                               value="{{ old('nomor_dokumen') }}" 
                               placeholder="Contoh: KW-2026/XI/004 atau SJ-8841"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label for="sumber_atau_tujuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Penyedia / Vendor Rekanan BPS
                        </label>
                        <input type="text" 
                               id="sumber_atau_tujuan" 
                               name="sumber_atau_tujuan" 
                               value="{{ old('sumber_atau_tujuan') }}" 
                               placeholder="Contoh: CV Sumber Makmur Cirebon"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Klasifikasi Status SPJ <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center p-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100/70 cursor-pointer transition">
                                <input type="radio" name="status_spj" value="selesai" {{ old('status_spj', 'selesai') === 'selesai' ? 'checked' : '' }} class="text-teal-600 focus:ring-teal-500">
                                <div class="ml-2.5">
                                    <span class="block text-xs font-bold text-slate-800">SPJ Selesai</span>
                                    <span class="block text-[10px] text-slate-500">Kwitansi lunas</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 rounded-2xl border border-amber-200 bg-amber-50/50 hover:bg-amber-50 cursor-pointer transition">
                                <input type="radio" name="status_spj" value="tertunda" {{ old('status_spj') === 'tertunda' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                                <div class="ml-2.5">
                                    <span class="block text-xs font-bold text-amber-900">Barang Langganan</span>
                                    <span class="block text-[10px] text-amber-700">SPJ menyusul</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Catatan / Keterangan Alokasi Pengadaan
                        </label>
                        <input type="text" 
                               id="keterangan" 
                               name="keterangan" 
                               value="{{ old('keterangan') }}" 
                               placeholder="Contoh: Pengadaan ATK Triwulan IV kegiatan Sensus Pertanian / Operasional Rutin"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    </div>
                </div>
            </div>

            <!-- Detail Barang Multi-Item Card -->
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">2. Rincian Barang yang Diterima</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tambahkan satu atau lebih item persediaan yang masuk ke gudang.</p>
                    </div>

                    <button type="button" 
                            @click="addItem()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold border border-teal-200 transition">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Baris Barang</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Nama Barang & Kode</th>
                                <th class="py-3 px-4 w-36 text-center">Stok Saat Ini</th>
                                <th class="py-3 px-4 w-40 text-center">Jumlah Diterima</th>
                                <th class="py-3 px-4 w-28 text-center">Satuan</th>
                                <th class="py-3 px-4 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                    <td class="py-3 px-4">
                                        <select :name="'items[' + index + '][barang_id]'" 
                                                x-model="item.barang_id" 
                                                @change="updateItemMeta(index)"
                                                required
                                                class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                            <option value="">-- Pilih Barang Persediaan --</option>
                                            <template x-for="b in barangs" :key="b.id">
                                                <option :value="b.id" x-text="b.kode + ' - ' + b.nama"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-block px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-mono font-bold" x-text="item.stok + ' ' + item.satuan"></span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <input type="number" 
                                               :name="'items[' + index + '][jumlah]'" 
                                               x-model.number="item.jumlah" 
                                               min="1" 
                                               required
                                               class="w-28 text-center py-2 px-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                    </td>
                                    <td class="py-3 px-4 text-center font-semibold text-slate-600" x-text="item.satuan"></td>
                                    <td class="py-3 px-4 text-center">
                                        <button type="button" 
                                                @click="removeItem(index)" 
                                                :disabled="items.length <= 1"
                                                :class="items.length <= 1 ? 'opacity-30 cursor-not-allowed' : 'text-rose-500 hover:text-rose-700 hover:bg-rose-50'"
                                                class="p-2 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <span class="text-xs text-slate-500">
                        Total Jenis Barang: <span class="font-bold text-slate-800" x-text="items.length"></span> Item
                    </span>
                    <button type="button" 
                            @click="addItem()" 
                            class="text-xs font-bold text-teal-700 hover:underline">
                        + Tambah Barang Lainnya
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.barang-masuk.index') }}" class="px-6 py-2.5 rounded-full border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                    <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan & Tambahkan ke Stok Gudang</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
