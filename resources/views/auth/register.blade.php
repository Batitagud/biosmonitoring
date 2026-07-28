<x-guest-layout>
    <div class="w-full max-w-sm mx-auto rounded-[28px] bg-[#eef1f5] p-8
            shadow-[10px_10px_24px_#d1d4d8,-10px_-10px_24px_#ffffff]">

        <div class="flex items-center justify-center gap-2 mb-6">
            <x-application-logo class="w-9 h-9 rounded-full object-cover" />
            <span class="font-bold text-lg text-slate-700">{{ config('app.name') }}</span>
        </div>

        <h1 class="text-center text-xl font-bold text-slate-800 mb-1">Daftar Akun</h1>
        <p class="text-center text-sm text-slate-500 mb-6">Bio-Sensor IoT Dashboard</p>

        <div id="auth-error" class="hidden bg-red-100 text-red-700 text-sm p-3 rounded-2xl mb-4"></div>

        <form id="register-form" class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nama Lengkap</label>
                <input type="text" id="name" placeholder="Masukkan nama lengkap" required
                    class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                          shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]
                          focus:shadow-[inset_5px_5px_10px_#c7cad0,inset_-5px_-5px_10px_#ffffff]">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Email</label>
                <input type="email" id="email" placeholder="Masukkan email" required
                    class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                          shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]
                          focus:shadow-[inset_5px_5px_10px_#c7cad0,inset_-5px_-5px_10px_#ffffff]">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Password</label>
                <input type="password" id="password" placeholder="Masukkan password (min. 6 karakter)" required minlength="6"
                    class="w-full rounded-full bg-[#eef1f5] px-5 py-3 text-sm text-slate-700 outline-none
                          shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]
                          focus:shadow-[inset_5px_5px_10px_#c7cad0,inset_-5px_-5px_10px_#ffffff]">
            </div>

            <button type="submit"
                class="w-full rounded-full py-3.5 font-semibold text-white
                       bg-gradient-to-r from-blue-500 to-blue-600
                       shadow-[4px_4px_12px_rgba(37,99,235,0.35)]
                       hover:from-blue-600 hover:to-blue-700 transition">
                Daftar
            </button>
        </form>

        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-slate-300"></div>
            <span class="text-xs text-slate-400">atau</span>
            <div class="flex-1 h-px bg-slate-300"></div>
        </div>

        <button id="google-btn"
            class="w-full flex items-center justify-center gap-2 rounded-full py-3 font-semibold text-slate-700 bg-[#eef1f5]
                   shadow-[4px_4px_10px_#d1d4d8,-4px_-4px_10px_#ffffff]
                   hover:shadow-[2px_2px_6px_#d1d4d8,-2px_-2px_6px_#ffffff] transition">
            <img src="https://www.google.com/favicon.ico" class="w-4 h-4" alt="">
            Daftar dengan Google
        </button>

        <p class="text-center text-sm text-slate-500 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-semibold">Masuk</a>
        </p>
    </div>

    @vite('resources/js/firebase-auth.js')
    <script>
        document.getElementById('register-form').addEventListener('submit', (e) => {
            e.preventDefault();
            window.handleRegister(
                document.getElementById('name').value,
                document.getElementById('email').value,
                document.getElementById('password').value
            );
        });
        document.getElementById('google-btn').addEventListener('click', () => window.handleGoogleLogin());
    </script>
</x-guest-layout>