<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Periksa Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Kelola antrian dan input hasil pemeriksaan pasien hari ini.
            </p>
        </div>
    </div>

    {{-- Alert Flash Message --}}
    @if (session('message'))
    <div class="alert alert-{{ session('type') == 'danger' ? 'error' : (session('type') ?? 'success') }} mb-4 rounded-xl shadow-sm text-white" role="alert">
        <i class="fas fa-{{ session('type') == 'danger' ? 'circle-xmark' : 'circle-check' }}"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4 text-center">No Antrian</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-sm">
                        @forelse ($daftarPasien as $dp)
                        @php
                            $isSelesai = $dp->periksas->isNotEmpty() || $dp->status === 'selesai';
                            $isSedangDilayani = $dp->status === 'sedang_dilayani';
                        @endphp
                        <tr class="hover:bg-slate-50 transition {{ $isSedangDilayani ? 'bg-indigo-50/50' : '' }}">

                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm border 
                                    {{ $isSedangDilayani ? 'bg-indigo-600 text-white border-indigo-700 shadow-md ring-2 ring-indigo-300' : 'bg-slate-100 text-slate-800 border-slate-200' }}">
                                    {{ $dp->no_antrian }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $dp->pasien->nama ?? $dp->pasien->name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate" title="{{ $dp->keluhan }}">
                                {{ $dp->keluhan ?: '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if ($isSelesai)
                                    <span class="badge bg-green-100 text-green-700 border border-green-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-circle-check mr-1"></i>Selesai
                                    </span>
                                @elseif ($isSedangDilayani)
                                    <span class="badge bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg px-3 py-1 text-xs font-semibold animate-pulse">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>Sedang Dilayani
                                    </span>
                                @else
                                    <span class="badge bg-amber-100 text-amber-700 border border-amber-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-clock mr-1"></i>Menunggu
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if ($isSelesai)
                                    <span class="text-xs text-slate-400 italic">Pemeriksaan Selesai</span>
                                @elseif ($isSedangDilayani)
                                    <a href="{{ route('periksa-pasien.create', $dp->id) }}"
                                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-none rounded-lg px-4 gap-1 shadow-sm">
                                        <i class="fas fa-stethoscope"></i>
                                        Periksa Sekarang
                                    </a>
                                @else
                                    <div class="inline-flex items-center gap-2">
                                        <form action="{{ route('periksa-pasien.panggil', $dp->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-none rounded-lg px-3 gap-1 shadow-sm" title="Panggil pasien dan update live ke dashboard">
                                                <i class="fas fa-bullhorn"></i>
                                                Panggil
                                            </button>
                                        </form>

                                        <a href="{{ route('periksa-pasien.create', $dp->id) }}"
                                            class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-3">
                                            <i class="fas fa-stethoscope"></i>
                                            Periksa
                                        </a>
                                    </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-14 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-inbox text-3xl"></i>
                                    <span>Tidak ada data pasien periksa</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 200);
            }
        }, 2500);
    </script>

</x-layouts.app>
