<x-layouts.app title="Pembayaran Tagihan">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Pembayaran Pemeriksaan
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Kelola tagihan biaya pemeriksaan dan unggah bukti transfer pembayaran Anda.
            </p>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-error mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-exclamation"></i>
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Card Tabel Tagihan --}}
    <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Poliklinik</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4 text-right">Total Tagihan</th>
                            <th class="px-6 py-4 text-center">Bukti Bayar</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($pembayarans as $pembayaran)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg px-2.5 py-1 text-xs">
                                    <i class="fas fa-hospital mr-1"></i>
                                    {{ $pembayaran->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                {{ $pembayaran->daftarPoli->jadwalPeriksa->dokter->nama ?? $pembayaran->daftarPoli->jadwalPeriksa->dokter->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($pembayaran->tgl_periksa)->translatedFormat('d M Y, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-600 text-base">
                                Rp {{ number_format($pembayaran->biaya_periksa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($pembayaran->bukti_pembayaran)
                                    <button onclick="previewImage('{{ asset('uploads/bukti_pembayaran/' . $pembayaran->bukti_pembayaran) }}')" 
                                            class="btn btn-xs bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-lg gap-1">
                                        <i class="fas fa-image text-indigo-500"></i>
                                        Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum Ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($pembayaran->status_pembayaran === 'lunas')
                                    <span class="badge bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-circle-check mr-1"></i>Lunas
                                    </span>
                                @elseif ($pembayaran->status_pembayaran === 'menunggu_verifikasi')
                                    <span class="badge bg-blue-100 text-blue-700 border border-blue-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="badge bg-amber-100 text-amber-700 border border-amber-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-clock mr-1"></i>Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($pembayaran->status_pembayaran === 'lunas')
                                    <span class="text-xs text-emerald-600 font-semibold flex items-center justify-end gap-1">
                                        <i class="fas fa-check-double"></i> Terverifikasi
                                    </span>
                                @else
                                    <button onclick="openUploadModal('{{ $pembayaran->id }}', 'Rp {{ number_format($pembayaran->biaya_periksa, 0, ',', '.') }}')"
                                            class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-none rounded-lg px-3 gap-1 shadow-sm">
                                        <i class="fas fa-upload"></i>
                                        {{ $pembayaran->bukti_pembayaran ? 'Ganti Bukti' : 'Upload Bukti' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-receipt text-4xl text-slate-300"></i>
                                    <span class="font-medium">Tidak ada tagihan pemeriksaan saat ini</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL UPLOAD BUKTI PEMBAYARAN --}}
    <dialog id="uploadModal" class="modal">
        <div class="modal-box rounded-2xl max-w-md">
            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2 mb-2">
                <i class="fas fa-file-arrow-up text-indigo-600"></i>
                Upload Bukti Pembayaran
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                Total Tagihan: <span id="modalTotalTagihan" class="font-bold text-indigo-600 text-sm"></span>
            </p>

            <form id="formUpload" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-control mb-4">
                    <label class="label pb-1.5">
                        <span class="text-xs font-semibold text-slate-700">Foto Bukti Transfer (Struk/Resi) <span class="text-red-500">*</span></span>
                    </label>
                    <input type="file" 
                           name="bukti_pembayaran" 
                           id="bukti_pembayaran_input" 
                           accept="image/jpeg,image/png,image/jpg" 
                           class="file-input file-input-bordered file-input-primary w-full rounded-xl text-sm"
                           required
                           onchange="previewModalImage(this)">
                    <span class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maks. 2MB)</span>
                </div>

                {{-- Preview Image Container --}}
                <div id="previewContainer" class="hidden mb-4">
                    <span class="text-xs font-semibold text-slate-600 block mb-1">Preview Gambar:</span>
                    <img id="imagePreview" src="#" alt="Preview Bukti" class="w-full max-h-56 object-contain rounded-xl border border-slate-200 bg-slate-50 p-1">
                </div>

                <div class="modal-action">
                    <button type="button" onclick="uploadModal.close()" class="btn btn-ghost rounded-xl text-slate-500">
                        Batal
                    </button>
                    <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-5 border-none">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Kirim Bukti
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    {{-- MODAL PREVIEW GAMBAR --}}
    <dialog id="previewModal" class="modal">
        <div class="modal-box rounded-2xl max-w-lg p-4">
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                <h4 class="font-bold text-slate-800 text-sm">Foto Bukti Transfer</h4>
                <button onclick="previewModal.close()" class="btn btn-xs btn-ghost btn-circle">✕</button>
            </div>
            <div class="flex justify-center p-2 bg-slate-900/5 rounded-xl">
                <img id="modalPreviewImg" src="" alt="Bukti Transfer" class="max-h-[70vh] rounded-lg object-contain shadow-sm">
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    {{-- Script Modal Handling --}}
    <script>
        function openUploadModal(periksaId, totalFormatted) {
            const form = document.getElementById('formUpload');
            form.action = `/pasien/pembayaran/${periksaId}/upload`;
            document.getElementById('modalTotalTagihan').textContent = totalFormatted;
            document.getElementById('previewContainer').classList.add('hidden');
            document.getElementById('bukti_pembayaran_input').value = '';
            document.getElementById('uploadModal').showModal();
        }

        function previewModalImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    document.getElementById('previewContainer').classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewImage(imageUrl) {
            document.getElementById('modalPreviewImg').src = imageUrl;
            document.getElementById('previewModal').showModal();
        }
    </script>

</x-layouts.app>
