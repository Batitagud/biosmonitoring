<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiosMonitoring</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|manrope:500,600,700,800|jetbrains-mono:500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #0f172a;
            --teal: #0d9488;
            --teal-deep: #0f766e;
            --mist: #eef1f5;
            --line: #d7dde5;
        }

        body {
            font-family: 'Figtree', sans-serif;
        }

        .font-display {
            font-family: 'Manrope', sans-serif;
        }

        .font-mono-data {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Signature: ECG / heartbeat line that draws itself in on load, then loops a subtle pulse */
        .ecg-path {
            stroke-dasharray: 1400;
            stroke-dashoffset: 1400;
            animation: ecg-draw 2.2s ease-out forwards, ecg-pulse 2.6s ease-in-out 2.2s infinite;
        }

        @keyframes ecg-draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes ecg-pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.55;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .ecg-path {
                animation: none;
                stroke-dashoffset: 0;
            }
        }

        .reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .reveal.in {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-[var(--mist)] text-slate-800 antialiased">

    <!-- NAV -->
    <header class="sticky top-0 z-40 border-b border-[var(--line)] bg-[var(--mist)]/90 backdrop-blur">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <x-application-logo class="w-8 h-8 rounded-full object-cover" />
                <span class="font-display font-bold text-slate-800 tracking-tight">BiosMonitoring</span>
            </a>

            <nav class="flex items-center gap-3">
                <a href="https://wa.me/6283872437225?text=Halo%20BiosMonitoring%2C%20saya%20ingin%20bertanya"
                    target="_blank" rel="noopener"
                    class="hidden sm:inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold text-slate-700 bg-[var(--mist)]
                          shadow-[3px_3px_8px_#d1d4d8,-3px_-3px_8px_#ffffff]
                          hover:shadow-[1px_1px_4px_#d1d4d8,-1px_-1px_4px_#ffffff] transition">
                    Contact Us
                </a>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center rounded-full px-5 py-2 text-sm font-semibold text-white
                          bg-gradient-to-r from-teal-600 to-teal-700
                          shadow-[3px_3px_10px_rgba(13,148,136,0.35)]
                          hover:from-teal-700 hover:to-teal-800 transition">
                    Login
                </a>
            </nav>
        </div>
    </header>

    <!-- HERO -->
    <section class="relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-6 pt-16 pb-20 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="font-mono-data text-xs tracking-[0.2em] text-teal-700 mb-4">REAL-TIME BIO MONITORING</p>
                <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight tracking-tight">
                    Setiap detak,<br>
                    <span class="text-teal-700">terpantau tanpa jeda.</span>
                </h1>
                <p class="mt-5 text-slate-600 text-base sm:text-lg max-w-md">
                    BiosMonitoring membaca sinyal vital dari sensor kamu dan menampilkannya
                    di satu dashboard — jelas, akurat, dan bisa diakses kapan saja.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center rounded-full px-6 py-3.5 font-semibold text-white
                              bg-gradient-to-r from-teal-600 to-teal-700
                              shadow-[4px_4px_14px_rgba(13,148,136,0.35)]
                              hover:from-teal-700 hover:to-teal-800 transition">
                        Mulai Pantau
                    </a>
                    <a href="https://wa.me/6281234567890?text=Halo%20BiosMonitoring%2C%20saya%20ingin%20bertanya"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 rounded-full px-6 py-3.5 font-semibold text-slate-700 bg-[var(--mist)]
                              shadow-[4px_4px_10px_#d1d4d8,-4px_-4px_10px_#ffffff]
                              hover:shadow-[2px_2px_6px_#d1d4d8,-2px_-2px_6px_#ffffff] transition">
                        Hubungi Kami
                    </a>
                </div>
            </div>

            <!-- Monitor card: signature ECG line + live-style readouts -->
            <div class="rounded-[28px] bg-[var(--mist)] p-6 sm:p-8
                        shadow-[10px_10px_24px_#d1d4d8,-10px_-10px_24px_#ffffff]">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-mono-data text-[11px] tracking-widest text-slate-400">SENSOR-01 · LIVE</span>
                    <span class="flex items-center gap-1.5 text-[11px] font-mono-data text-teal-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span> ONLINE
                    </span>
                </div>

                <svg viewBox="0 0 400 100" class="w-full h-24" xmlns="http://www.w3.org/2000/svg">
                    <polyline class="ecg-path" fill="none" stroke="#0d9488" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                        points="0,50 40,50 55,50 65,20 75,80 85,10 95,50 110,50 150,50 165,50 175,30 185,65 195,50 230,50 270,50 285,50 295,25 305,75 315,50 400,50" />
                </svg>

                <div class="grid grid-cols-3 gap-3 mt-4">
                    <div class="rounded-2xl bg-[var(--mist)] p-3 text-center shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                        <p class="font-mono-data text-lg font-semibold text-slate-800">78</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">BPM</p>
                    </div>
                    <div class="rounded-2xl bg-[var(--mist)] p-3 text-center shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                        <p class="font-mono-data text-lg font-semibold text-slate-800">98%</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">SpO₂</p>
                    </div>
                    <div class="rounded-2xl bg-[var(--mist)] p-3 text-center shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                        <p class="font-mono-data text-lg font-semibold text-slate-800">36.6°</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">SUHU</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="max-w-6xl mx-auto px-6 py-16">
        <p class="reveal font-mono-data text-xs tracking-[0.2em] text-teal-700 text-center mb-2">KENAPA BIOSMONITORING</p>
        <h2 class="reveal font-display text-2xl sm:text-3xl font-bold text-slate-900 text-center max-w-xl mx-auto">
            Data sensor yang bisa kamu percaya, kapan saja dibutuhkan.
        </h2>

        <div class="grid sm:grid-cols-3 gap-6 mt-12">
            <div class="reveal rounded-[24px] bg-[var(--mist)] p-6 shadow-[8px_8px_18px_#d1d4d8,-8px_-8px_18px_#ffffff]">
                <div class="w-11 h-11 rounded-2xl bg-teal-600/10 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1.5">Data Real-Time</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Setiap pembacaan sensor langsung masuk ke dashboard, tanpa delay, tanpa refresh manual.
                </p>
            </div>

            <div class="reveal rounded-[24px] bg-[var(--mist)] p-6 shadow-[8px_8px_18px_#d1d4d8,-8px_-8px_18px_#ffffff]">
                <div class="w-11 h-11 rounded-2xl bg-teal-600/10 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1.5">Aman & Tersimpan</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Riwayat data tersimpan rapi di cloud, bisa ditelusuri kapan pun kamu butuh.
                </p>
            </div>

            <div class="reveal rounded-[24px] bg-[var(--mist)] p-6 shadow-[8px_8px_18px_#d1d4d8,-8px_-8px_18px_#ffffff]">
                <div class="w-11 h-11 rounded-2xl bg-teal-600/10 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1.5">Notifikasi Dini</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Dapat peringatan begitu ada bacaan di luar batas normal — sebelum jadi masalah.
                </p>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS (genuine sequence, numbering justified) -->
    <section class="bg-white/60 py-16 border-y border-[var(--line)]">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="reveal font-display text-2xl sm:text-3xl font-bold text-slate-900 text-center mb-12">
                Mulai memantau dalam 3 langkah
            </h2>
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="reveal text-center">
                    <p class="font-mono-data text-3xl font-bold text-teal-700/30 mb-2">01</p>
                    <h3 class="font-display font-bold text-slate-800 mb-1.5">Pasang Sensor</h3>
                    <p class="text-sm text-slate-500">Hubungkan perangkat bio-sensor ke jaringan yang tersedia.</p>
                </div>
                <div class="reveal text-center">
                    <p class="font-mono-data text-3xl font-bold text-teal-700/30 mb-2">02</p>
                    <h3 class="font-display font-bold text-slate-800 mb-1.5">Sinkronisasi Akun</h3>
                    <p class="text-sm text-slate-500">Daftar dan tautkan perangkat ke akun BiosMonitoring kamu.</p>
                </div>
                <div class="reveal text-center">
                    <p class="font-mono-data text-3xl font-bold text-teal-700/30 mb-2">03</p>
                    <h3 class="font-display font-bold text-slate-800 mb-1.5">Pantau Dashboard</h3>
                    <p class="text-sm text-slate-500">Lihat data vital secara langsung, dari perangkat apa saja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA BANNER -->
    <section class="max-w-6xl mx-auto px-6 py-16">
        <div class="reveal rounded-[28px] bg-gradient-to-r from-teal-600 to-teal-800 px-8 py-10 sm:px-12 sm:py-12
                    flex flex-col sm:flex-row items-center justify-between gap-6
                    shadow-[8px_8px_24px_rgba(13,148,136,0.25)]">
            <div class="text-center sm:text-left">
                <h3 class="font-display text-xl sm:text-2xl font-bold text-white">
                    Siap pantau kondisi tubuh secara real-time?
                </h3>
                <p class="text-teal-50 text-sm mt-1">Masuk ke dashboard kamu atau hubungi tim kami kalau ada pertanyaan.</p>
            </div>
            <div class="flex gap-3 shrink-0">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center rounded-full px-6 py-3 font-semibold text-teal-700 bg-white
                          hover:bg-teal-50 transition">
                    Login
                </a>
                <a href="https://wa.me/6281234567890?text=Halo%20BiosMonitoring%2C%20saya%20ingin%20bertanya"
                    target="_blank" rel="noopener"
                    class="inline-flex items-center rounded-full px-6 py-3 font-semibold text-white border border-white/40
                          hover:bg-white/10 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-[var(--line)]">
        <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <x-application-logo class="w-6 h-6 rounded-full object-cover" />
                <span class="font-display font-semibold text-sm text-slate-700">BiosMonitoring</span>
            </div>
            <p class="text-xs text-slate-400">© {{ date('Y') }} BiosMonitoring. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script>
        const revealEls = document.querySelectorAll('.reveal');
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) e.target.classList.add('in');
            });
        }, {
            threshold: 0.15
        });
        revealEls.forEach(el => io.observe(el));
    </script>
</body>

</html>