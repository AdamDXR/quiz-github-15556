<x-layouts.app title="Semua Notifikasi">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Pusat Notifikasi
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Riwayat seluruh pemberitahuan dan status aktivitas akun Anda.
            </p>
        </div>

        @if(auth()->user()->unreadNotifications->isNotEmpty())
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl gap-1.5 shadow-sm">
                <i class="fas fa-check-double text-xs"></i>
                Tandai Semua Sudah Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Alert --}}
    @if (session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Card Container --}}
    <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="divide-y divide-slate-100">
                @forelse ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                    $icon = $data['icon'] ?? 'fa-bell';
                    $color = $data['color'] ?? 'indigo';
                @endphp
                <div class="p-5 flex items-start gap-4 transition-all duration-200 {{ $isUnread ? 'bg-indigo-50/40 border-l-4 border-l-indigo-600' : 'hover:bg-slate-50/60' }}">

                    {{-- Icon Badge --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                        {{ $isUnread ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                        <i class="fas {{ $icon }} text-base"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-sm font-bold {{ $isUnread ? 'text-slate-900' : 'text-slate-700' }}">
                                {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                            </h4>
                            @if($isUnread)
                                <span class="badge bg-indigo-100 text-indigo-700 border-none text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                    Baru
                                </span>
                            @endif
                        </div>

                        <p class="text-xs {{ $isUnread ? 'text-slate-800 font-medium' : 'text-slate-500' }} leading-relaxed mb-2">
                            {{ $data['message'] ?? '-' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-clock text-[10px]"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>

                            @if(!empty($data['link']))
                            <a href="{{ $data['link'] }}" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 hover:underline">
                                Buka Halaman Terkait
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="shrink-0">
                        @if($isUnread)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-ghost text-indigo-600 hover:bg-indigo-100 rounded-lg" title="Tandai sudah dibaca">
                                <i class="fas fa-check mr-1"></i>
                                Tandai Dibaca
                            </button>
                        </form>
                        @else
                        <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                            <i class="fas fa-check-double text-[10px] text-slate-400"></i>
                            Sudah dibaca
                        </span>
                        @endif
                    </div>

                </div>
                @empty
                <div class="text-center py-16 text-slate-400">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <i class="fas fa-bell-slash text-4xl text-slate-300"></i>
                        <span class="font-medium">Belum ada notifikasi untuk Anda</span>
                        <p class="text-xs text-slate-400">Pemberitahuan terkait pembayaran dan pemeriksaan akan muncul di sini.</p>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
            @endif

        </div>
    </div>

</x-layouts.app>
