<x-layouts::petugas_app :title="__('Dashboard Petugas')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <x-managed-message />

        {{-- 1. HERO CARD (Sambutan Personal) --}}
        <flux:card class="relative overflow-hidden border-none bg-emerald-600 text-white shadow-md p-8!">
            {{-- Efek dekorasi background --}}
            <div class="absolute -right-10 -top-20 opacity-10">
                <flux:icon.truck class="w-64 h-64" />
            </div>

            <div class="relative z-10">
                <flux:subheading class="text-emerald-100 mb-1">Selamat datang, Petugas Kebersihan</flux:subheading>
                <flux:heading size="2xl" class="text-white mb-4">{{ $worker->nama_petugas ?? auth()->user()->name }}</flux:heading>

                <div class="flex flex-wrap gap-4 text-sm mt-4">
                    <div class="flex items-center gap-1.5 bg-emerald-700/50 px-3 py-1.5 rounded-md">
                        <flux:icon.truck class="w-4 h-4 text-emerald-300" />
                        <span>Kendaraan: {{ $worker->jenis_kendaraan ?? '-' }} ({{ $worker->plat_nomor ?? '-' }})</span>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-6! border-l-4 border-l-indigo-500">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-50 text-indigo-600 p-3 rounded-full dark:bg-indigo-900/30">
                    <flux:icon.map-pin class="w-7 h-7" />
                </div>

                <div>
                    <flux:subheading class="dark:text-white">Lokasi TPS Penugasan</flux:subheading>
                    <flux:heading size="lg">
                        {{ $worker->tps->nama_tps ?? 'Belum ada TPS yang ditugaskan' }}
                        @if($worker->tps)
                            <span class="text-sm font-normal text-zinc-500 dark:text-white">
                                (Kecamatan: {{ ucfirst($worker->tps->kecamatan) }})
                            </span>
                        @endif
                    </flux:heading>
                </div>
            </div>
        </flux:card>

        {{-- 2. STATISTIC / ACTION CARDS --}}
        <div class="grid gap-4 md:grid-cols-2">
            
            {{-- Status Tugas Terakhir --}}
            <flux:card class="flex items-center gap-4 p-6! border-t-4 border-t-amber-500">
                <div class="bg-amber-50 text-amber-600 p-3 rounded-full dark:bg-amber-900/30">
                    <flux:icon.clipboard-document-check class="w-7 h-7" />
                </div>
                <div>
                    <flux:subheading class="dark:text-white">Laporan Hari Ini</flux:subheading>
                    <flux:heading size="lg">Belum Dilaporkan</flux:heading>
                </div>
            </flux:card>

            {{-- Pintasan Aksi --}}
            <flux:card
                class="flex items-center gap-4 p-6! border-t-4 border-t-blue-500 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition"
                onclick="window.location.href='{{ route('petugas.laporan') }}'">
                <div class="bg-blue-50 text-blue-600 p-3 rounded-full dark:bg-blue-900/30">
                    <flux:icon.paper-airplane class="w-7 h-7" />
                </div>
                <div>
                    <flux:subheading class="dark:text-white">Formulir Laporan</flux:subheading>
                    <flux:heading size="md" class="text-blue-600 dark:text-white">Buat Laporan Baru &rarr;</flux:heading>
                </div>
            </flux:card>
        </div>

    </div>
</x-layouts::petugas_app>
