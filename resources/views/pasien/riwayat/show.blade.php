<x-layouts.app title="Detail Hasil Pemeriksaan">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.riwayat.index') }}" class="inline-flex items-center justify-center w-9 h-9 
                  rounded-lg bg-slate-100 text-slate-500 
                  hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Detail Pemeriksaan
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Rincian catatan medis, resep obat, dan total biaya pemeriksaan Anda.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Informasi Kunjungan & Catatan Dokter --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Info Kunjungan --}}
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">
                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center gap-2">
                        <i class="fas fa-hospital-user text-indigo-500"></i>
                        Data Kunjungan
                    </h3>

                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between items-center pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500">Poliklinik</span>
                            <span class="font-semibold text-slate-800">
                                {{ $riwayat->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500">Dokter Pemeriksa</span>
                            <span class="font-semibold text-slate-800">
                                {{ $riwayat->jadwalPeriksa->dokter->nama ?? $riwayat->jadwalPeriksa->dokter->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500">Tanggal Periksa</span>
                            <span class="font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="flex justify-between items-center pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500">Nomor Antrian</span>
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs">
                                {{ $riwayat->no_antrian }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-500 block mb-1">Keluhan Pasien:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs leading-relaxed">
                                {{ $riwayat->keluhan ?: 'Tidak ada catatan keluhan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan Dokter --}}
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">
                    <h3 class="font-bold text-slate-800 text-base mb-3 flex items-center gap-2">
                        <i class="fas fa-notes-medical text-emerald-500"></i>
                        Catatan & Diagnosis Dokter
                    </h3>
                    <div class="bg-emerald-50/60 border border-emerald-200/60 rounded-xl p-4 text-sm text-slate-700 leading-relaxed">
                        {{ $periksa->catatan ?: 'Tidak ada catatan khusus dari dokter.' }}
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Rincian Obat & Total Biaya --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6 sm:p-8">

                    <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-prescription-bottle-medical text-indigo-500"></i>
                        Daftar Obat yang Diresepkan
                    </h3>

                    <div class="overflow-x-auto mb-6">
                        <table class="table w-full">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider border-y border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">No</th>
                                    <th class="py-3 px-4">Nama Obat</th>
                                    <th class="py-3 px-4">Kemasan</th>
                                    <th class="py-3 px-4 text-right">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                @php $totalObat = 0; @endphp
                                @forelse ($periksa->detailPeriksas as $detail)
                                @php $totalObat += $detail->obat->harga ?? 0; @endphp
                                <tr>
                                    <td class="py-3 px-4 text-slate-400 font-medium">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">
                                        {{ $detail->obat->nama_obat ?? 'Obat tidak ditemukan' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">
                                        {{ $detail->obat->kemasan ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium text-slate-700">
                                        Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-slate-400 italic">
                                        Tidak ada resep obat untuk pemeriksaan ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Ringkasan Biaya --}}
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                        <h4 class="font-bold text-slate-800 text-sm mb-4 uppercase tracking-wider">
                            Rincian Pembayaran
                        </h4>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Jasa Pemeriksaan Dokter</span>
                                <span class="font-medium text-slate-800">Rp 150.000</span>
                            </div>

                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal Resep Obat ({{ $periksa->detailPeriksas->count() }} item)</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($totalObat, 0, ',', '.') }}</span>
                            </div>

                            <div class="pt-3 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-base font-bold text-slate-800">Total Biaya Pemeriksaan</span>
                                <span class="text-xl font-extrabold text-indigo-600">
                                    Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Status Pembayaran --}}
                        <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500">Status Pembayaran:</span>
                                @if($periksa->status_pembayaran === 'lunas')
                                    <span class="badge bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold px-3 py-1 text-xs">
                                        <i class="fas fa-circle-check mr-1"></i>Lunas
                                    </span>
                                @elseif($periksa->status_pembayaran === 'menunggu_verifikasi')
                                    <span class="badge bg-blue-100 text-blue-700 border border-blue-200 font-semibold px-3 py-1 text-xs">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>Menunggu Verifikasi Admin
                                    </span>
                                @else
                                    <span class="badge bg-amber-100 text-amber-700 border border-amber-200 font-semibold px-3 py-1 text-xs">
                                        <i class="fas fa-clock mr-1"></i>Belum Dibayar
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
