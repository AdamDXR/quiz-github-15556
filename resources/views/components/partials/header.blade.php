<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-6 gap-4 sticky top-0 z-30 shadow-sm">

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" class="btn btn-square btn-ghost lg:hidden">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-base-content/50">Poliklinik</span>
            <i data-lucide="chevron-right" class="w-4 h-4 text-base-content/30"></i>
            <span class="font-semibold text-base-content">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Fullscreen --}}
    <button onclick="toggleFullscreen()" class="btn btn-square btn-ghost" title="Layar Penuh">
        <i id="fsIcon" class="fas fa-expand w-5 h-5"></i>
    </button>

    {{-- Notification Dropdown --}}
    @php
        $userNotifications = auth()->user()->notifications()->take(5)->get();
        $unreadCount = auth()->user()->unreadNotifications()->count();
    @endphp

    <div class="dropdown dropdown-end">
        <button tabindex="0" class="btn btn-square btn-ghost relative" title="Notifikasi">
            <i class="fas fa-bell w-5 h-5 text-slate-600"></i>
            @if($unreadCount > 0)
                <span class="absolute top-2 right-2 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>

        <div tabindex="0" class="dropdown-content z-[100] menu p-0 shadow-xl bg-base-100 rounded-2xl w-80 sm:w-96 border border-slate-200 overflow-hidden">
            {{-- Header Dropdown --}}
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 text-sm">Notifikasi</span>
                    @if($unreadCount > 0)
                        <span class="badge bg-indigo-100 text-indigo-700 border-none text-xs font-semibold px-2 py-0.5">
                            {{ $unreadCount }} Baru
                        </span>
                    @endif
                </div>
                @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold hover:underline">
                        Tandai Semua Dibaca
                    </button>
                </form>
                @endif
            </div>

            {{-- Body List --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                @forelse($userNotifications as $item)
                @php
                    $isUnreadItem = is_null($item->read_at);
                    $notifData = $item->data;
                @endphp
                <div class="p-3.5 flex items-start gap-3 transition {{ $isUnreadItem ? 'bg-indigo-50/50' : 'hover:bg-slate-50/70' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $isUnreadItem ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                        <i class="fas {{ $notifData['icon'] ?? 'fa-bell' }} text-xs"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-xs font-bold {{ $isUnreadItem ? 'text-slate-900' : 'text-slate-700' }} truncate">
                                {{ $notifData['title'] ?? 'Pemberitahuan' }}
                            </span>
                            <span class="text-[10px] text-slate-400 shrink-0">
                                {{ $item->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                        <p class="text-xs {{ $isUnreadItem ? 'text-slate-800 font-medium' : 'text-slate-500' }} line-clamp-2 leading-relaxed">
                            {{ $notifData['message'] ?? '-' }}
                        </p>
                    </div>

                    @if($isUnreadItem)
                    <form action="{{ route('notifications.read', $item->id) }}" method="POST" class="shrink-0 self-center">
                        @csrf
                        <button type="submit" class="w-6 h-6 rounded-full hover:bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs" title="Tandai dibaca">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                </div>
                @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-bell-slash text-2xl mb-2 text-slate-300"></i>
                    <p class="text-xs">Tidak ada notifikasi saat ini</p>
                </div>
                @endforelse
            </div>

            {{-- Footer Dropdown --}}
            <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                    Lihat Semua Notifikasi
                </a>
            </div>
        </div>
    </div>

    {{-- User Info --}}
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 hover:opacity-80 transition group" title="Buka Profil & Ganti Password">

        <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold leading-tight group-hover:text-primary transition">
                {{ auth()->user()->nama ?? auth()->user()->name ?? 'Pengguna' }}
            </div>
            <div class="text-xs text-base-content/50 leading-tight">
                {{ ucfirst(auth()->user()->role ?? 'Admin Sistem') }}
            </div>
        </div>

        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center shadow-sm">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </a>

</header>

<script>
    function toggleFullscreen() {
    const icon = document.getElementById('fsIcon');

    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        icon.classList.remove('fa-expand');
        icon.classList.add('fa-compress');
    } else {
        document.exitFullscreen();
        icon.classList.remove('fa-compress');
        icon.classList.add('fa-expand');
    }
}
</script>
