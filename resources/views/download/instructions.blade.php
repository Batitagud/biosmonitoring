<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-700 leading-tight">Download Widget</h2>
    </x-slot>

    <div class="py-8 bg-[#eef1f5] min-h-screen">
        <div class="max-w-2xl mx-auto px-4">

            <div class="rounded-[24px] bg-[#eef1f5] p-8 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <h3 class="text-lg font-bold text-slate-800 mb-1">Sebelum Download</h3>
                <p class="text-sm text-slate-500 mb-6">Baca dulu instruksi ini biar widget-nya bisa langsung dipakai.</p>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-7 h-7 shrink-0 rounded-full bg-blue-500 text-white text-sm font-bold flex items-center justify-center">1</div>
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">Punya akun & sudah pairing kode perangkat</p>
                            <p class="text-sm text-slate-500 mt-0.5">Kalau belum, daftar/login dulu di web ini, terus pasangkan kode perangkat (menu setelah login) sebelum pakai widget.</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-7 h-7 shrink-0 rounded-full bg-blue-500 text-white text-sm font-bold flex items-center justify-center">2</div>
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">Login pakai Google? Atur password dulu</p>
                            <p class="text-sm text-slate-500 mt-0.5">
                                Widget desktop cuma bisa login pakai <strong>email + password</strong> (belum support Google).
                                Kalau akun kamu daftar/login pakai Google, buka
                                <a href="{{ route('profile.edit') }}" class="text-blue-600 font-semibold underline">halaman Profile</a>,
                                cari bagian Password, klik "Kirim Kode ke Email Saya", ikuti instruksinya buat atur password.
                                Setelah itu, akun kamu bisa dipakai login di widget juga.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-7 h-7 shrink-0 rounded-full bg-blue-500 text-white text-sm font-bold flex items-center justify-center">3</div>
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">Ekstrak & jalankan</p>
                            <p class="text-sm text-slate-500 mt-0.5">Setelah download, ekstrak file .zip-nya. Semua file (BiosensorMonitor.exe + 4 file model) harus tetap dalam satu folder yang sama, jangan dipisah.</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-7 h-7 shrink-0 rounded-full bg-blue-500 text-white text-sm font-bold flex items-center justify-center">4</div>
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">Login → masukin kode perangkat → START</p>
                            <p class="text-sm text-slate-500 mt-0.5">Widget bakal minta email+password akun kamu, terus kode perangkat 6 karakter, baru bisa mulai monitoring.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl bg-[#eef1f5] p-4 text-sm text-amber-600 shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                    ⚠️ Windows mungkin nunjukin peringatan "Windows protected your PC" pas pertama kali buka .exe-nya —
                    itu normal buat aplikasi tanpa sertifikat resmi, bukan virus. Klik "More info" → "Run anyway".
                </div>

                <div class="mt-8 rounded-2xl border-2 border-blue-200 bg-white p-5">
                    <a href="{{ route('download.widget') }}"
                        class="block w-full text-center rounded-full py-4 font-semibold text-white
                              bg-gradient-to-r from-blue-500 to-blue-600
                              shadow-[4px_4px_12px_rgba(37,99,235,0.35)]
                              hover:from-blue-600 hover:to-blue-700 transition">
                        ⬇ Download Widget (.zip)
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>