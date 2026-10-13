<x-layouts.app title="Jadwal Periksa">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Jadwal Periksa
        </h2>

        <a href="{{ route('jadwal-periksa.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5
                  bg-primary hover:bg-primary/90
                  text-white text-sm font-semibold
                  rounded-xl transition">
            <i class="fas fa-plus text-xs"></i>
            Tambah Jadwal
        </a>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Table Head --}}
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-16">No</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Mulai</th>
                            <th class="px-6 py-4">Jam Selesai</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($jadwals as $jadwal)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                            <td class="px-6 py-4 text-slate-500">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-indigo-500 text-xs"></i>
                                    {{ $jadwal->hari }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} WIB
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                {{ date('H:i', strtotime($jadwal->jam_selesai)) }} WIB
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('jadwal-periksa.edit', $jadwal->id) }}" class="inline-flex items-center gap-1 px-4 py-2
                                              bg-amber-500 hover:bg-amber-600
                                              text-white text-xs font-semibold
                                              rounded-lg transition">
                                        <i class="fas fa-pen text-xs"></i>
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('jadwal-periksa.destroy', $jadwal->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus jadwal periksa ini?')" class="inline-flex items-center gap-1 px-4 py-2
                                                   bg-red-500 hover:bg-red-600
                                                   text-white text-xs font-semibold
                                                   rounded-lg transition">
                                            <i class="fas fa-trash text-xs"></i>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-calendar-xmark text-3xl mb-3 block"></i>
                                Belum ada jadwal periksa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>
