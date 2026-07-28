<x-guest-layout>
    <div class="w-full max-w-sm mx-auto rounded-[28px] bg-[#eef1f5] p-8
            shadow-[10px_10px_24px_#d1d4d8,-10px_-10px_24px_#ffffff]">

        <div class="flex items-center justify-center gap-2 mb-6">
            <x-application-logo class="w-9 h-9 rounded-full object-cover" />
            <span class="font-bold text-lg text-slate-700">{{ config('app.name') }}</span>
        </div>

        <h1 class="text-center text-xl font-bold text-slate-800 mb-1">Verifikasi Email</h1>
        <p class="text-center text-sm text-slate-500 mb-6">
            Kode 6 digit sudah dikirim ke <span class="font-semibold">{{ $email }}</span>
        </p>

        @if (session('success'))
        <div class="rounded-2xl bg-[#eef1f5] p-3 mb-4 text-sm text-emerald-600 shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('verify.email.submit') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" maxlength="6" placeholder="KODE 6 DIGIT" autofocus
                class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-center tracking-widest font-bold text-slate-700 outline-none
                      shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
            @error('code') <p class="text-red-500 text-xs text-center mt-1.5">{{ $message }}</p> @enderror

            <button type="submit"
                class="w-full rounded-full py-3.5 font-semibold text-white
                       bg-gradient-to-r from-blue-500 to-blue-600
                       shadow-[4px_4px_12px_rgba(37,99,235,0.35)]
                       hover:from-blue-600 hover:to-blue-700 transition">
                Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('verify.email.resend') }}" class="mt-4">
            @csrf
            <button class="w-full text-sm text-blue-600 font-semibold text-center">Kirim ulang kode</button>
        </form>
    </div>
</x-guest-layout>