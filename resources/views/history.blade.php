<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Pemakaian</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4">

            @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
            @endif

            @if ($sessions->isEmpty())
            <div class="bg-white rounded-xl border p-8 text-center text-gray-500">
                Belum ada riwayat sesi.
            </div>
            @else
            <div class="space-y-5">
                @foreach ($sessions as $s)
                @php
                $start = isset($s['started_at']) ? \Carbon\Carbon::createFromTimestamp($s['started_at']) : null;
                $end = isset($s['ended_at']) ? \Carbon\Carbon::createFromTimestamp($s['ended_at']) : null;
                @endphp
                <div class="bg-white rounded-xl border p-6" id="history-card-{{ $s['id'] }}">

                    {{-- Header: nama, legend, tombol --}}
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="font-bold text-gray-800">{{ $name }}</div>
                            <div class="flex gap-2 mt-2" data-html2canvas-ignore>
                                <button data-download-id="{{ $s['id'] }}" class="btn-download-history
                                                bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs font-semibold px-3 py-1.5 rounded">
                                    Download
                                </button>
                                <form method="POST" action="{{ route('history.destroy', $s['id']) }}"
                                    onsubmit="return confirm('Hapus riwayat ini?')">
                                    @csrf @method('DELETE')
                                    <button class="bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold px-3 py-1.5 rounded">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-xs space-y-1">
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-green-500 inline-block"></span> normal</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-500 inline-block"></span> stress</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-orange-400 inline-block"></span> fatig</div>
                        </div>
                    </div>

                    {{-- Bar dengan waktu di luar kiri-kanan --}}
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600 whitespace-nowrap">{{ $start ? $start->format('H:i') : '--:--' }}</span>

                        <div class="flex-1 h-8 rounded-md overflow-hidden flex">
                            @if ($s['normal_pct'] > 0)
                            <div class="bg-green-500 flex items-center justify-center text-xs text-black font-semibold"
                                style="width: {{ $s['normal_pct'] }}%">
                                {{ $s['normal_pct'] >= 8 ? round($s['normal_pct']).'%' : '' }}
                            </div>
                            @endif
                            @if ($s['stres_pct'] > 0)
                            <div class="bg-red-500 flex items-center justify-center text-xs text-black font-semibold"
                                style="width: {{ $s['stres_pct'] }}%">
                                {{ $s['stres_pct'] >= 8 ? round($s['stres_pct']).'%' : '' }}
                            </div>
                            @endif
                            @if ($s['lelah_pct'] > 0)
                            <div class="bg-orange-400 flex items-center justify-center text-xs text-black font-semibold"
                                style="width: {{ $s['lelah_pct'] }}%">
                                {{ $s['lelah_pct'] >= 8 ? round($s['lelah_pct']).'%' : '' }}
                            </div>
                            @endif
                        </div>

                        <span class="text-sm text-gray-600 whitespace-nowrap">{{ $end ? $end->format('H:i') : '--:--' }}</span>
                    </div>

                    @if (isset($s['avg_bpm']))
                    <div class="text-xs text-gray-400 mt-2">
                        Rata² BPM: {{ round($s['avg_bpm']) }} ·
                        {{ $start && $end ? round($start->diffInSeconds($end) / 60).' menit' : '' }} ·
                        {{ $start ? $start->translatedFormat('d M Y') : '' }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.querySelectorAll('.btn-download-history').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-download-id');
                const el = document.getElementById('history-card-' + id);
                html2canvas(el, {
                    backgroundColor: '#ffffff',
                    scale: 2
                }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = 'riwayat-' + id + '.jpg';
                    link.href = canvas.toDataURL('image/jpeg', 0.95);
                    link.click();
                });
            });
        });
    </script>
</x-app-layout>