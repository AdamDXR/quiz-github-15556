<x-layouts.app title="Riwayat Pendaftaran Poli">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pendaftaran Poli
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Daftar histori pendaftaran dan hasil pemeriksaan Anda di Poliklinik.
            </p>
        </div>
        <a href="{{ route('pasien.daftar') }}" class="btn bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 border-none shadow-sm">
            <i class="fas fa-plus mr-1.5"></i>
            Daftar Poli Baru
        </a>
    </div>

    {{-- Alert Flash Message --}}
    @if (session('error'))
    <div class="alert alert-error mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Card Tabel Riwayat --}}
    <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Poliklinik</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Jadwal Periksa</th>
                            <th class="px-6 py-4 text-center">No. Antrian</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($riwayats as $riwayat)
                        @php
                            $isPeriksa = $riwayat->periksas->isNotEmpty();
                            $periksa = $riwayat->periksas->first();
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg px-2.5 py-1 text-xs">
                                    <i class="fas fa-hospital mr-1"></i>
                                    {{ $riwayat->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                {{ $riwayat->jadwalPeriksa->dokter->nama ?? $riwayat->jadwalPeriksa->dokter->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div class="font-medium">{{ $riwayat->jadwalPeriksa->hari ?? '-' }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ substr($riwayat->jadwalPeriksa->jam_mulai ?? '', 0, 5) }} - {{ substr($riwayat->jadwalPeriksa->jam_selesai ?? '', 0, 5) }} WIB
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-800 font-bold text-sm border border-slate-200">
                                    {{ $riwayat->no_antrian }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate" title="{{ $riwayat->keluhan }}">
                                {{ $riwayat->keluhan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($isPeriksa)
                                    <span class="badge bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-circle-check mr-1"></i>Sudah Diperiksa
                                    </span>
                                @else
                                    <span class="badge bg-amber-100 text-amber-700 border border-amber-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-clock mr-1"></i>Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($isPeriksa)
                                    <a href="{{ route('pasien.riwayat.show', $riwayat->id) }}"
                                       class="btn btn-sm bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg px-3 gap-1">
                                        <i class="fas fa-file-waveform"></i>
                                        Detail
                                    </a>
                                @else
                                    <button disabled class="btn btn-sm btn-ghost text-slate-300 rounded-lg px-3 cursor-not-allowed" title="Pemeriksaan belum dilakukan">
                                        <i class="fas fa-hourglass-start"></i>
                                        Menunggu
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-folder-open text-4xl text-slate-300"></i>
                                    <span class="font-medium">Belum ada riwayat pendaftaran poli</span>
                                    <a href="{{ route('pasien.daftar') }}" class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg mt-2 border-none">
                                        Daftar Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>
