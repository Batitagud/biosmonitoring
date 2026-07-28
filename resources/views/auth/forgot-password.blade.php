<x-guest-layout>
    <div class="w-full max-w-sm mx-auto rounded-[28px] bg-[#eef1f5] p-8
            shadow-[10px_10px_24px_#d1d4d8,-10px_-10px_24px_#ffffff]">

        <div class="flex items-center justify-center gap-2 mb-6">
            <x-application-logo class="w-9 h-9 rounded-full object-cover" />
            <span class="font-bold text-lg text-slate-700">{{ config('app.name') }}</span>
        </div>

        <h1 class="text-center text-xl font-bold text-slate-800 mb-1">Lupa Password</h1>
        <p class="text-center text-sm text-slate-500 mb-6">Masukkan email, kami kirim kode buat reset password.</p>

        @if (session('success'))
        <div class="bg-green-100 text-green-700 text-sm p-3 rounded-2xl mb-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="text-xs font-semibold text-slate-600 mb-1.5 block">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email" required
                    class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                          shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]
                          focus:shadow-[inset_5px_5px_10px_#c7cad0,inset_-5px_-5px_10px_#ffffff]">
                @error('email') <p class="text-red-500 text-xs mt-1.5 ml-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full rounded-full py-3.5 font-semibold text-white
                       bg-gradient-to-r from-blue-500 to-blue-600
                       shadow-[4px_4px_12px_rgba(37,99,235,0.35)]
                       hover:from-blue-600 hover:to-blue-700 transition">
                Kirim Kode
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold">← Kembali ke login</a>
        </p>
    </div>
</x-guest-layout>