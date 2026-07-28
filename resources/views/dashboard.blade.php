<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Bio-Sensor IoT Dashboard</h2>
                <p class="text-sm text-slate-600">Pemantauan status kognitif, detak jantung, suhu kulit, dan GSR secara real-time.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full bg-[#eef1f5] px-4 py-2 text-sm font-medium text-emerald-600
                        shadow-[3px_3px_8px_#d1d4d8,-3px_-3px_8px_#ffffff]">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                Live Stream
            </div>
        </div>
    </x-slot>

    <div class="bg-[#eef1f5] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-blue-600">Monitoring Medis</p>
                        <h3 class="mt-1 text-2xl font-semibold text-slate-800">Dashboard Bio-Sensor Real-Time</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="latency-badge" class="text-xs text-slate-400"></span>
                        <div id="connection-badge" class="inline-flex items-center gap-2 rounded-full bg-[#eef1f5] px-4 py-2 text-sm font-medium text-amber-600
                                    shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            Menunggu koneksi Firebase
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff] transition-all duration-300">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Nilai GSR</p>
                    <div id="nilai_gsr" class="mt-5 text-3xl font-semibold text-slate-800">--</div>
                    <p class="mt-2 text-sm text-slate-500">Konduktivitas kulit (raw)</p>
                </div>

                <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff] transition-all duration-300">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Detak Jantung</p>
                    <div id="nilai_bpm" class="mt-5 text-3xl font-semibold text-slate-800">-- BPM</div>
                    <p class="mt-2 text-sm text-slate-500">Nilai terbaru dari sensor bpm</p>
                </div>

                <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff] transition-all duration-300">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Suhu Kulit</p>
                    <div id="nilai_suhu" class="mt-5 text-3xl font-semibold text-slate-800">-- °C</div>
                    <p class="mt-2 text-sm text-slate-500">Nilai terbaru dari sensor suhu kulit</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff]">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-slate-800">Grafik Sensor</h3>
                        <p class="text-sm text-slate-500">BPM, Suhu Kulit, dan GSR Raw — real-time</p>
                    </div>
                    <div id="loading-chart" class="flex h-[260px] items-center justify-center rounded-2xl bg-[#eef1f5] shadow-[inset_4px_4px_8px_#d1d4d8,inset_-4px_-4px_8px_#ffffff]">
                        <div class="flex items-center gap-3 text-sm text-slate-500">
                            <span class="inline-block h-3 w-3 animate-pulse rounded-full bg-blue-500"></span>
                            Menunggu data sensor...
                        </div>
                    </div>
                    <div class="relative hidden h-[260px]" id="chart-wrap-main">
                        <canvas id="chartMain"></canvas>
                    </div>
                </div>

                <div id="card-status" class="rounded-[24px] bg-[#eef1f5] p-6 shadow-[8px_8px_16px_#d1d4d8,-8px_-8px_16px_#ffffff] transition-all duration-300">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Status Kondisi</p>
                            <div id="status_kognitif" class="mt-2 text-2xl font-semibold text-slate-800">Menunggu Data...</div>
                        </div>
                        <button id="history-button" type="button" onclick="document.getElementById('history-modal').classList.remove('hidden'); renderHistoryCsv();"
                            class="rounded-full bg-[#eef1f5] px-4 py-2 text-sm font-medium text-slate-600 whitespace-nowrap
                                       shadow-[3px_3px_8px_#d1d4d8,-3px_-3px_8px_#ffffff]
                                       hover:shadow-[1px_1px_4px_#d1d4d8,-1px_-1px_4px_#ffffff] transition">
                            History
                        </button>
                    </div>
                    <div id="advice-box" class="rounded-2xl bg-[#eef1f5] p-4 text-sm text-slate-600 leading-relaxed
                                shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]">
                        Menunggu data sensor buat kasih saran...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="history-modal" class="fixed inset-0 z-50 hidden bg-slate-950/50 px-4 py-8">
        <div class="mx-auto flex h-full max-w-4xl flex-col rounded-[28px] bg-[#eef1f5] shadow-[10px_10px_24px_rgba(0,0,0,0.25)]">
            <div class="flex items-center justify-between px-5 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Riwayat Sensor</h3>
                    <p class="text-sm text-slate-500">Data sensor dalam format CSV yang terus bertambah.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button id="download-history" type="button"
                        class="rounded-full bg-[#eef1f5] px-4 py-2 text-sm font-medium text-emerald-600
                                   shadow-[3px_3px_8px_#d1d4d8,-3px_-3px_8px_#ffffff]
                                   hover:shadow-[1px_1px_4px_#d1d4d8,-1px_-1px_4px_#ffffff] transition">
                        Download CSV
                    </button>
                    <button id="close-history" type="button" onclick="document.getElementById('history-modal').classList.add('hidden');"
                        class="rounded-full bg-[#eef1f5] px-4 py-2 text-sm font-medium text-slate-600
                                   shadow-[3px_3px_8px_#d1d4d8,-3px_-3px_8px_#ffffff]
                                   hover:shadow-[1px_1px_4px_#d1d4d8,-1px_-1px_4px_#ffffff] transition">
                        Tutup
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-auto p-5">
                <pre id="history-csv" class="whitespace-pre-wrap rounded-2xl bg-slate-950 p-4 text-sm text-slate-100">timestamp,bpm,suhu_kulit,gsr_raw,predicted_label
</pre>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const connectionBadge = document.getElementById('connection-badge');
        const latencyBadge = document.getElementById('latency-badge');
        const statusCard = document.getElementById('card-status');
        const loadingChart = document.getElementById('loading-chart');
        const chartWrapMain = document.getElementById('chart-wrap-main');
        const chartMainEl = document.getElementById('chartMain');
        const adviceBox = document.getElementById('advice-box');
        const historyButton = document.getElementById('history-button');
        const historyModal = document.getElementById('history-modal');
        const historyCsvEl = document.getElementById('history-csv');
        const downloadHistoryButton = document.getElementById('download-history');
        const closeHistoryButton = document.getElementById('close-history');

        const MAX_POINTS = 30;
        const MAX_HISTORY = 100;
        const historyEntries = [];
        const bpmLabels = [];
        const bpmData = [];
        const tempData = [];
        const gsrData = [];

        const LABEL_MAP = {
            1: 'Normal',
            2: 'Stres',
            3: 'Fatigue'
        };

        const mainChart = new Chart(chartMainEl, {
            type: 'line',
            data: {
                labels: bpmLabels,
                datasets: [{
                        label: 'BPM',
                        data: bpmData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.08)',
                        tension: 0.25,
                        fill: false,
                        pointRadius: 0,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Suhu Kulit (°C)',
                        data: tempData,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15,118,110,0.08)',
                        tension: 0.25,
                        fill: false,
                        pointRadius: 0,
                        yAxisID: 'y'
                    },
                    {
                        label: 'GSR Raw',
                        data: gsrData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.08)',
                        tension: 0.25,
                        fill: false,
                        pointRadius: 0,
                        yAxisID: 'yGsr'
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                scales: {
                    y: {
                        position: 'left',
                        title: {
                            display: true,
                            text: 'BPM / Suhu (°C)'
                        }
                    },
                    yGsr: {
                        position: 'right',
                        title: {
                            display: true,
                            text: 'GSR Raw'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                },
            },
        });

        function formatMetric(value, suffix = '') {
            if (value === null || value === undefined || Number.isNaN(Number(value))) return `--${suffix}`;
            return `${Number(value).toFixed(1)}${suffix}`;
        }

        function updateMetric(id, value, suffix = '') {
            const el = document.getElementById(id);
            if (el) el.textContent = formatMetric(value, suffix);
        }

        function updateConnectionState(connected) {
            if (!connectionBadge) return;
            const base = 'inline-flex items-center gap-2 rounded-full bg-[#eef1f5] px-4 py-2 text-sm font-medium shadow-[inset_3px_3px_6px_#d1d4d8,inset_-3px_-3px_6px_#ffffff]';
            if (connected) {
                connectionBadge.className = base + ' text-emerald-600';
                connectionBadge.innerHTML = '<span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Terhubung ke Firebase';
            } else {
                connectionBadge.className = base + ' text-amber-600';
                connectionBadge.innerHTML = '<span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>Menunggu koneksi Firebase';
            }
        }

        function updateStatusCard(label) {
            const el = document.getElementById('status_kognitif');
            if (!el) return;
            const safeLabel = label || 'Menunggu Data...';
            el.textContent = safeLabel;
            el.className = 'mt-2 text-2xl font-semibold ';
            if (safeLabel === 'Normal') el.classList.add('text-emerald-600');
            else if (safeLabel === 'Stres') el.classList.add('text-rose-600');
            else if (safeLabel === 'Fatigue') el.classList.add('text-amber-600');
            else el.classList.add('text-slate-800');
        }

        function showCharts() {
            loadingChart?.classList.add('hidden');
            chartWrapMain?.classList.remove('hidden');
        }

        function updateAdvice(label) {
            if (!adviceBox) return;
            const ADVICE = {
                'Normal': 'Kondisi kamu terlihat stabil. Tetap jaga pola istirahat, hidrasi, dan lanjutkan aktivitas seperti biasa.',
                'Stres': 'Terdeteksi tanda-tanda stres. Coba tarik napas dalam beberapa kali, istirahat sejenak dari layar, atau lakukan peregangan ringan.',
                'Fatigue': 'Terdeteksi tanda kelelahan. Sebaiknya istirahat sebentar, minum air putih, dan hindari aktivitas berat untuk beberapa saat.',
            };
            adviceBox.textContent = ADVICE[label] ?? 'Menunggu data sensor buat kasih saran...';
        }

        function pushPoint(arr, value, maxPoints = MAX_POINTS) {
            arr.push(value);
            if (arr.length > maxPoints) arr.shift();
        }

        function renderHistoryCsv() {
            if (!historyCsvEl) return;
            if (historyEntries.length === 0) {
                historyCsvEl.textContent = 'timestamp,bpm,suhu_kulit,gsr_raw,predicted_label\n';
                return;
            }
            const rows = historyEntries.map((e) => {
                const safeLabel = String(e.predicted_label ?? '').replace(/"/g, '""');
                return `${e.timestamp},${e.bpm},${e.suhu},${e.gsr},"${safeLabel}"`;
            });
            historyCsvEl.textContent = `timestamp,bpm,suhu_kulit,gsr_raw,predicted_label\n${rows.join('\n')}`;
        }

        function downloadHistoryCsv() {
            if (historyEntries.length === 0) return;
            const csv = `timestamp,bpm,suhu_kulit,gsr_raw,predicted_label\n${historyEntries.map((e) => {
                const safeLabel = String(e.predicted_label ?? '').replace(/"/g, '""');
                return `${e.timestamp},${e.bpm},${e.suhu},${e.gsr},"${safeLabel}"`;
            }).join('\n')}`;
            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'sensor-history.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        historyButton?.addEventListener('click', renderHistoryCsv);
        closeHistoryButton?.addEventListener('click', () => historyModal.classList.add('hidden'));
        downloadHistoryButton?.addEventListener('click', downloadHistoryCsv);
        historyModal?.addEventListener('click', (e) => {
            if (e.target === historyModal) historyModal.classList.add('hidden');
        });

        // ── Polling ke Laravel backend (bukan langsung Firebase dari browser) ──
        async function pollLiveData() {
            try {
                const res = await fetch("{{ route('dashboard.live') }}");
                if (!res.ok) {
                    updateConnectionState(false);
                    return;
                }
                const d = await res.json();
                if (!d || Object.keys(d).length === 0) {
                    updateConnectionState(false);
                    return;
                }

                updateConnectionState(true);
                if (d.latency_sec !== undefined && latencyBadge) {
                    latencyBadge.textContent = `${d.latency_sec} detik lalu`;
                }

                const bpm = Number(d.bpm ?? 0);
                const suhu = Number(d.suhu ?? 0);
                const gsr = Number(d.gsr ?? 0);
                const label = LABEL_MAP[d.label] ?? 'Menunggu Data...';

                showCharts();
                updateMetric('nilai_bpm', bpm, ' BPM');
                updateMetric('nilai_suhu', suhu, ' °C');
                updateMetric('nilai_gsr', gsr, '');
                updateStatusCard(label);
                updateAdvice(label);

                const timeLabel = new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                const timestamp = new Date().toLocaleString('id-ID');
                pushPoint(bpmLabels, timeLabel);
                pushPoint(bpmData, bpm);
                pushPoint(tempData, suhu);
                pushPoint(gsrData, gsr);

                historyEntries.push({
                    timestamp,
                    bpm,
                    suhu,
                    gsr,
                    predicted_label: label
                });
                if (historyEntries.length > MAX_HISTORY) historyEntries.shift();

                mainChart.update('none');
            } catch (e) {
                console.error('Gagal ambil live data:', e);
                updateConnectionState(false);
            }
        }
        setInterval(pollLiveData, 2000);
        pollLiveData();
    </script>
    @endpush
</x-app-layout>