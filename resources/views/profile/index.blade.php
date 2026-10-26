<x-layouts.app title="Profil & Ganti Password">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Profil Saya
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Kelola informasi akun dan perbarui kata sandi Anda.
            </p>
        </div>
    </div>

    {{-- Success Flash Alert --}}
    @if (session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Informasi Akun --}}
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">
                    <div class="flex flex-col items-center text-center pb-6 border-b border-slate-100">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center text-2xl font-bold shadow-md mb-3">
                            {{ strtoupper(substr($user->nama ?? $user->name ?? 'U', 0, 1)) }}
                        </div>
                        <h3 class="font-bold text-lg text-slate-800">{{ $user->nama ?? $user->name }}</h3>
                        <span class="inline-block px-3 py-0.5 text-xs font-semibold rounded-full mt-1
                            {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : '' }}
                            {{ $user->role === 'dokter' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $user->role === 'pasien' ? 'bg-amber-100 text-amber-700' : '' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>

                    <div class="space-y-4 pt-4 text-sm">
                        <div>
                            <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider">Email</span>
                            <p class="font-medium text-slate-700">{{ $user->email }}</p>
                        </div>

                        @if($user->no_rm)
                        <div>
                            <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider">No. Rekam Medis (RM)</span>
                            <p class="font-medium text-slate-700">{{ $user->no_rm }}</p>
                        </div>
                        @endif

                        @if($user->no_ktp)
                        <div>
                            <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider">No. KTP</span>
                            <p class="font-medium text-slate-700">{{ $user->no_ktp }}</p>
                        </div>
                        @endif

                        @if($user->no_hp)
                        <div>
                            <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider">No. Telepon / HP</span>
                            <p class="font-medium text-slate-700">{{ $user->no_hp }}</p>
                        </div>
                        @endif

                        @if($user->alamat)
                        <div>
                            <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider">Alamat</span>
                            <p class="font-medium text-slate-700">{{ $user->alamat }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Ganti Password --}}
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6 sm:p-8">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-lock text-indigo-500"></i>
                            Ganti Password
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Demi keamanan akun, pastikan password baru memiliki panjang minimal 8 karakter dan berbeda dari password lama.
                        </p>
                    </div>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Password Lama --}}
                        <div class="form-control mb-5">
                            <label class="label pb-1.5" for="current_password">
                                <span class="text-sm font-semibold text-slate-700">
                                    Password Lama <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password"
                                       class="input input-bordered w-full rounded-xl pr-10 @error('current_password') input-error @enderror"
                                       placeholder="Masukkan password lama Anda"
                                       required>
                                <button type="button" 
                                        onclick="togglePasswordVisibility('current_password', this)" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div class="form-control mb-5">
                            <label class="label pb-1.5" for="password">
                                <span class="text-sm font-semibold text-slate-700">
                                    Password Baru <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password" 
                                       name="password"
                                       class="input input-bordered w-full rounded-xl pr-10 @error('password') input-error @enderror"
                                       placeholder="Minimal 8 karakter"
                                       required>
                                <button type="button" 
                                        onclick="togglePasswordVisibility('password', this)" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="form-control mb-6">
                            <label class="label pb-1.5" for="password_confirmation">
                                <span class="text-sm font-semibold text-slate-700">
                                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       class="input input-bordered w-full rounded-xl pr-10"
                                       placeholder="Ulangi password baru Anda"
                                       required>
                                <button type="button" 
                                        onclick="togglePasswordVisibility('password_confirmation', this)" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="reset" class="btn btn-ghost rounded-xl text-slate-500">
                                Batal
                            </button>
                            <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-6 border-none shadow-sm">
                                <i class="fas fa-key mr-1.5"></i>
                                Simpan Password Baru
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Script toggle visibility --}}
    <script>
        function togglePasswordVisibility(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</x-layouts.app>
