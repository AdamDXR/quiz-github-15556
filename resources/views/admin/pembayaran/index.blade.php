<x-layouts.app title="Verifikasi Pembayaran Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Verifikasi Pembayaran
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Periksa bukti transfer dan konfirmasi pelunasan tagihan pemeriksaan pasien.
            </p>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Card Tabel Verifikasi --}}
    <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Poliklinik / Dokter</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4 text-right">Total Tagihan</th>
                            <th class="px-6 py-4 text-center">Bukti Transfer</th>
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
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ $pembayaran->daftarPoli->pasien->nama ?? $pembayaran->daftarPoli->pasien->name ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    RM: {{ $pembayaran->daftarPoli->pasien->no_rm ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div class="font-semibold text-indigo-600">
                                    {{ $pembayaran->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    dr. {{ $pembayaran->daftarPoli->jadwalPeriksa->dokter->nama ?? $pembayaran->daftarPoli->jadwalPeriksa->dokter->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($pembayaran->tgl_periksa)->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800 text-base">
                                Rp {{ number_format($pembayaran->biaya_periksa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($pembayaran->bukti_pembayaran)
                                    <button onclick="previewBukti('{{ asset('uploads/bukti_pembayaran/' . $pembayaran->bukti_pembayaran) }}')"
                                            class="btn btn-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg gap-1">
                                        <i class="fas fa-image"></i>
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
                                    <span class="badge bg-blue-100 text-blue-700 border border-blue-200 rounded-lg px-3 py-1 text-xs font-semibold animate-pulse">
                                        <i class="fas fa-clock mr-1"></i>Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="badge bg-amber-100 text-amber-700 border border-amber-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-hourglass mr-1"></i>Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($pembayaran->status_pembayaran === 'menunggu_verifikasi')
                                    <form action="{{ route('admin.pembayaran.konfirmasi', $pembayaran->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengonfirmasi bahwa pembayaran ini sudah lunas?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg border-none px-3 gap-1 shadow-sm">
                                            <i class="fas fa-check"></i>
                                            Konfirmasi
                                        </button>
                                    </form>
                                @elseif ($pembayaran->status_pembayaran === 'lunas')
                                    <span class="text-xs text-slate-400 font-medium italic">
                                        Diverifikasi {{ $pembayaran->tgl_verifikasi ? \Carbon\Carbon::parse($pembayaran->tgl_verifikasi)->format('d/m/Y') : '' }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">
                                        Menunggu Pasien
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-folder-open text-4xl text-slate-300"></i>
                                    <span class="font-medium">Tidak ada data tagihan pemeriksaan</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW GAMBAR ADMIN --}}
    <dialog id="previewBuktiModal" class="modal">
        <div class="modal-box rounded-2xl max-w-lg p-4">
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                <h4 class="font-bold text-slate-800 text-sm">Foto Bukti Transfer Pasien</h4>
                <button onclick="previewBuktiModal.close()" class="btn btn-xs btn-ghost btn-circle">✕</button>
            </div>
            <div class="flex justify-center p-2 bg-slate-900/5 rounded-xl">
                <img id="adminPreviewImg" src="" alt="Bukti Transfer" class="max-h-[70vh] rounded-lg object-contain shadow-sm">
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function previewBukti(imageUrl) {
            document.getElementById('adminPreviewImg').src = imageUrl;
            document.getElementById('previewBuktiModal').showModal();
        }
    </script>

</x-layouts.app>
