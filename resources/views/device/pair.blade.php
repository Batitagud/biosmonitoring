<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pasangkan Perangkat</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-sm mx-auto bg-white p-8 rounded-xl border text-center">
            <p class="text-gray-500 text-sm mb-6">
                Masukkan kode 6 karakter yang di-set di ESP32 kamu untuk mulai memantau.
            </p>

            @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('info'))
            <div class="bg-blue-100 text-blue-700 p-3 rounded mb-4 text-sm">{{ session('info') }}</div>
            @endif

            <form method="POST" action="{{ route('device.pair.store') }}">
                @csrf
                <input type="text" name="device_code" maxlength="6" placeholder="KODE 6 KARAKTER"
                    class="border rounded px-3 py-3 w-full uppercase text-center tracking-widest font-bold" required>
                @error('device_code')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                <button class="bg-blue-600 text-white px-4 py-3 rounded mt-4 w-full font-semibold">
                    Pasangkan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>