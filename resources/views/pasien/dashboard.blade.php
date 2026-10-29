<x-layouts.app title="Dashboard Pasien">

    {{-- Welcome Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Dashboard Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Selamat datang kembali, <span class="font-semibold text-indigo-600">{{ auth()->user()->nama ?? auth()->user()->name }}</span>. Pantau antrian Anda secara langsung.
            </p>
        </div>

        <div>
            <a href="{{ route('pasien.daftar') }}" class="btn bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm gap-2 border-none">
                <i class="fas fa-notes-medical"></i>
                Daftar Poli Baru
            </a>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session('message'))
    <div class="alert alert-{{ session('type') == 'danger' ? 'error' : (session('type') ?? 'success') }} mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-{{ session('type') == 'danger' ? 'circle-xmark' : 'circle-check' }}"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- ================= 1. BANNER ANTRIAN AKTIF ================= --}}
    <div class="mb-8" id="bannerAntrianWrapper">
        @if ($antrianAktif)
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-800 text-white p-6 sm:p-8 shadow-xl border border-indigo-400/30">
            
            {{-- Background Accent Circles --}}
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-indigo-400/20 blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                {{-- Header Badge --}}
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur-md border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Antrian Aktif Anda
                    </span>
                    <span class="text-xs text-white/80 font-medium">
                        Diperbarui Otomatis (Live)
                    </span>
                </div>

                {{-- Banner Body Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    
                    {{-- Info Poli & Dokter --}}
                    <div class="lg:col-span-7">
                        <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-2">
                            {{ $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-indigo-100">
                            <span class="flex items-center gap-1.5 font-medium">
                                <i class="fas fa-user-doctor text-indigo-300"></i>
                                dr. {{ $antrianAktif->jadwalPeriksa->dokter->nama ?? $antrianAktif->jadwalPeriksa->dokter->name ?? '-' }}
                            </span>
                            <span class="hidden sm:inline text-white/40">•</span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-calendar-day text-indigo-300"></i>
                                {{ $antrianAktif->jadwalPeriksa->hari }} ({{ substr($antrianAktif->jadwalPeriksa->jam_mulai, 0, 5) }} - {{ substr($antrianAktif->jadwalPeriksa->jam_selesai, 0, 5) }} WIB)
                            </span>
                        </div>

                        @if($antrianAktif->keluhan)
                        <div class="mt-4 pt-3 border-t border-white/15 text-xs text-indigo-100">
                            <span class="text-white/60">Keluhan:</span> {{ $antrianAktif->keluhan }}
                        </div>
                        @endif
                    </div>

                    {{-- Counter Antrian Boxes --}}
                    <div class="lg:col-span-5 flex items-center justify-start lg:justify-end gap-3 sm:gap-4">
                        
                        {{-- Box Nomor Antrian Pasien --}}
                        <div class="flex-1 max-w-[150px] bg-white/15 backdrop-blur-md rounded-2xl p-4 text-center border border-white/20 shadow-inner">
                            <span class="block text-[11px] uppercase tracking-wider font-semibold text-indigo-200 mb-1">
                                Nomor Anda
                            </span>
                            <span class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                                {{ $antrianAktif->no_antrian }}
                            </span>
                        </div>

                        {{-- Box Nomor Sedang Dilayani --}}
                        <div class="flex-1 max-w-[170px] bg-white text-slate-900 rounded-2xl p-4 text-center shadow-2xl relative overflow-hidden group">
                            <div class="flex items-center justify-center gap-1.5 mb-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-500">
                                    Sedang Dilayani
                                </span>
                            </div>
                            <span id="bannerSedangDilayani" class="text-3xl sm:text-4xl font-black text-indigo-600 tracking-tight transition-all duration-300">
                                {{ $nomorSedangDilayani }}
                            </span>
                            <span class="block text-[10px] text-emerald-600 font-bold mt-0.5">
                                LIVE UPDATE
                            </span>
                        </div>

                    </div>

                </div>
            </div>

        </div>
        @else
        {{-- Empty State Banner --}}
        <div class="card bg-base-100 shadow-sm rounded-3xl border border-slate-200 p-8 text-center">
            <div class="max-w-md mx-auto flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-4 shadow-inner">
                    <i class="fas fa-ticket"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">
                    Anda Belum Memiliki Antrian Aktif
                </h3>
                <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                    Daftar ke poli untuk mendapatkan nomor antrian pemeriksaan hari ini dan pantau panggilan nomor antrian secara real-time.
                </p>
                <a href="{{ route('pasien.daftar') }}" class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-5 border-none shadow-sm gap-2">
                    <i class="fas fa-plus"></i>
                    Daftar Antrian Poli Sekarang
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= 2. TABEL DAFTAR JADWAL & ANTRIAN REAL-TIME ================= --}}
    <div class="card bg-base-100 shadow-sm rounded-3xl border border-slate-200">
        <div class="card-body p-6 sm:p-8">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-6 border-b border-slate-100 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-hospital text-indigo-600"></i>
                        Status Antrian Poliklinik & Dokter
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar jadwal seluruh poliklinik beserta nomor antrian yang sedang dipanggil saat ini.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        WebSocket Reverb Aktif
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider rounded-xl">
                        <tr>
                            <th class="py-3.5 px-4">No</th>
                            <th class="py-3.5 px-4">Poliklinik</th>
                            <th class="py-3.5 px-4">Dokter</th>
                            <th class="py-3.5 px-4">Hari Praktik</th>
                            <th class="py-3.5 px-4">Jam Periksa</th>
                            <th class="py-3.5 px-4 text-center">Sedang Dilayani</th>
                            <th class="py-3.5 px-4 text-center">Total Antrian</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($jadwals as $jadwal)
                        <tr class="hover:bg-slate-50/70 transition" id="row-jadwal-{{ $jadwal->id }}">
                            <td class="py-4 px-4 text-slate-400 font-medium">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold px-3 py-1 text-xs">
                                    {{ $jadwal->dokter->poli->nama_poli ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800">
                                dr. {{ $jadwal->dokter->nama ?? $jadwal->dokter->name ?? '-' }}
                            </td>
                            <td class="py-4 px-4 font-medium text-slate-700">
                                {{ $jadwal->hari }}
                            </td>
                            <td class="py-4 px-4 text-slate-500">
                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span id="nomor-dilayani-{{ $jadwal->id }}" 
                                      class="inline-flex items-center justify-center w-9 h-9 rounded-xl font-black text-sm transition-all duration-300
                                      {{ $jadwal->nomor_dilayani !== '-' ? 'bg-indigo-600 text-white shadow-md ring-2 ring-indigo-200 scale-105' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $jadwal->nomor_dilayani }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                                    {{ $jadwal->total_antrian }} Pasien
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Belum ada jadwal praktik dokter yang tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- ================= 3. SCRIPT REAL-TIME ANTRIAN (WEBSOCKET & DUAL-FALLBACK) ================= --}}
    <script>
        // Animasi flash ketika nomor berubah
        function highlightChange(element, newValue) {
            if (!element) return;
            if (element.textContent.trim() !== String(newValue).trim()) {
                element.textContent = newValue;
                element.classList.add('bg-amber-400', 'text-slate-900', 'scale-125');
                setTimeout(() => {
                    element.classList.remove('bg-amber-400', 'text-slate-900', 'scale-125');
                }, 600);
            }
        }

        // 1. Sinkronisasi Data Antrian Real-Time (Polling Cadangan jika WebSocket Offline saat Demo)
        function syncAntrianData() {
            fetch("{{ route('pasien.antrian.live') }}")
                .then(res => res.json())
                .then(data => {
                    // Update Banner Sedang Dilayani jika antrian aktif ada
                    const bannerSedang = document.getElementById('bannerSedangDilayani');
                    if (bannerSedang && data.nomor_sedang_dilayani) {
                        highlightChange(bannerSedang, data.nomor_sedang_dilayani);
                    }

                    // Update Nomor Sedang Dilayani di tabel per jadwal
                    if (data.jadwal_status) {
                        for (const [jadwalId, noDilayani] of Object.entries(data.jadwal_status)) {
                            const badge = document.getElementById(`nomor-dilayani-${jadwalId}`);
                            if (badge) {
                                highlightChange(badge, noDilayani);
                                if (noDilayani !== '-') {
                                    badge.classList.remove('bg-slate-100', 'text-slate-400');
                                    badge.classList.add('bg-indigo-600', 'text-white', 'shadow-md');
                                } else {
                                    badge.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
                                    badge.classList.add('bg-slate-100', 'text-slate-400');
                                }
                            }
                        }
                    }
                })
                .catch(err => console.log('Sync status:', err));
        }

        // Jalankan sinkronisasi periodik setiap 4 detik untuk memastikan tabel selalu live
        setInterval(syncAntrianData, 4000);

        // 2. Listener WebSocket Laravel Echo jika terkonfigurasi
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.channel('antrian-channel')
                    .listen('.AntrianUpdated', (e) => {
                        console.log('Antrian Real-time Event Received:', e);
                        syncAntrianData();
                    });
            }
        });
    </script>

</x-layouts.app>
