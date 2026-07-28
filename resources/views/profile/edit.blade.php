<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-700 leading-tight">Profil</h2>
    </x-slot>

    <div class="py-8 bg-[#eef1f5]">
        <div class="max-w-2xl mx-auto px-4 space-y-5">

            @if (session('success'))
            <div class="rounded-2xl bg-[#eef1f5] p-4 text-sm text-emerald-600 shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                {{ session('success') }}
            </div>
            @endif

            {{-- FOTO PROFIL --}}
            <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <h3 class="font-semibold text-slate-700 mb-4">Foto Profil</h3>
                <div class="flex items-center gap-4">
                    <img src="{{ $photoUrl ?? 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=3b82f6&color=fff' }}"
                        class="w-16 h-16 rounded-full object-cover shadow-[3px_3px_8px_#d1d4d8,-3px_-3px_8px_#ffffff]" alt="Foto profil">
                    <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="flex-1">
                        @csrf
                        <input type="file" name="photo" accept="image/*" required class="text-sm mb-2 block text-slate-600">
                        @error('photo') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        <button class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm px-4 py-2 font-semibold
                                       shadow-[3px_3px_10px_rgba(37,99,235,0.35)] hover:from-blue-600 hover:to-blue-700 transition">
                            Upload Foto
                        </button>
                    </form>
                </div>
            </div>

            {{-- NAMA --}}
            <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <h3 class="font-semibold text-slate-700 mb-4">Nama</h3>
                <form method="POST" action="{{ route('profile.name.update') }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" value="{{ old('name', $name) }}" required
                        class="flex-1 rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                                  shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                    <button class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2 text-sm font-semibold
                                   shadow-[3px_3px_10px_rgba(37,99,235,0.35)] hover:from-blue-600 hover:to-blue-700 transition">
                        Simpan
                    </button>
                </form>
                @error('name') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- EMAIL --}}
            <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <h3 class="font-semibold text-slate-700 mb-1">Email</h3>
                <p class="text-sm text-slate-500 mb-4">Saat ini: <span class="font-medium">{{ $email }}</span></p>

                @if (!$pendingNewEmail)
                <form method="POST" action="{{ route('profile.email.request') }}" class="flex gap-2">
                    @csrf
                    <input type="email" name="new_email" placeholder="Email baru" required
                        class="flex-1 rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                                      shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                    <button class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2 text-sm font-semibold
                                       shadow-[3px_3px_10px_rgba(37,99,235,0.35)] hover:from-blue-600 hover:to-blue-700 transition">
                        Kirim Kode
                    </button>
                </form>
                @error('new_email') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                @else
                <div class="rounded-2xl bg-[#eef1f5] p-3 mb-3 text-sm text-blue-600 shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                    Kode verifikasi udah dikirim ke <strong>{{ $pendingNewEmail }}</strong>.
                </div>
                <form method="POST" action="{{ route('profile.email.confirm') }}" class="flex gap-2 mb-2">
                    @csrf
                    <input type="text" name="code" maxlength="6" placeholder="KODE 6 DIGIT" required
                        class="flex-1 rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-center tracking-widest font-bold text-slate-700 outline-none
                                      shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                    <button class="rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-5 py-2 text-sm font-semibold
                                       shadow-[3px_3px_10px_rgba(16,185,129,0.35)] transition">
                        Konfirmasi
                    </button>
                </form>
                @error('code') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
                <form method="POST" action="{{ route('profile.email.cancel') }}">
                    @csrf
                    <button class="text-xs text-slate-400 underline">Batalkan</button>
                </form>
                @endif
            </div>

            {{-- PASSWORD --}}
            <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <h3 class="font-semibold text-slate-700 mb-1">Password</h3>
                <p class="text-sm text-slate-500 mb-4">Perlu verifikasi kode ke email kamu sebelum bisa diganti.</p>

                @if (!$passwordCodeSent)
                <form method="POST" action="{{ route('profile.password.request') }}">
                    @csrf
                    <button class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2 text-sm font-semibold
                                       shadow-[3px_3px_10px_rgba(37,99,235,0.35)] hover:from-blue-600 hover:to-blue-700 transition">
                        Kirim Kode ke Email Saya
                    </button>
                </form>
                @else
                <div class="rounded-2xl bg-[#eef1f5] p-3 mb-3 text-sm text-blue-600 shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                    Kode verifikasi udah dikirim ke email kamu ({{ $email }}).
                </div>
                <form method="POST" action="{{ route('profile.password.confirm') }}" class="space-y-2">
                    @csrf
                    <input type="text" name="code" maxlength="6" placeholder="KODE 6 DIGIT" required
                        class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-center tracking-widest font-bold text-slate-700 outline-none
                                      shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                    @error('code') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    <input type="password" name="password" placeholder="Password Baru" required minlength="6"
                        class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                                      shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                    <input type="password" name="password_confirmation" placeholder="Ulangi Password Baru" required
                        class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                                      shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                    @error('password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    <button class="w-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3 text-sm font-semibold
                                       shadow-[3px_3px_10px_rgba(16,185,129,0.35)] transition">
                        Konfirmasi & Ganti Password
                    </button>
                </form>
                @endif
            </div>

            {{-- INFO LAIN --}}
            <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <div class="text-sm text-slate-500">
                    Kode Perangkat: <span class="font-mono font-medium text-slate-700">{{ $deviceCode ?? '- belum dipasangkan -' }}</span>
                </div>
            </div>

            <a href="{{ route('history') }}" class="block rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]
                       hover:shadow-[4px_4px_10px_#d1d4d8,-4px_-4px_10px_#ffffff] transition">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-700">Riwayat Pemakaian</h3>
                        <p class="text-sm text-slate-500 mt-1">Lihat riwayat kondisi tiap sesi pemakaian alat.</p>
                    </div>
                    <span class="text-slate-400">→</span>
                </div>
            </a>

        </div>
    </div>
</x-app-layout>