<x-layouts::pegawai_app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- 1. HERO CARD (Sambutan Personal) --}}
        <flux:card class="relative overflow-hidden border-none bg-emerald-600 text-white shadow-md p-8!">
            {{-- Efek dekorasi background --}}
            <div class="absolute -right-10 -top-20 opacity-10">
                <flux:icon.globe-americas class="w-64 h-64" />
            </div>

            <div class="relative z-10">
                <flux:subheading class="text-emerald-100 mb-1">Selamat datang kembali,</flux:subheading>
                <flux:heading size="2xl" class="text-white mb-4">{{ $employee->name }}</flux:heading>

                <div class="flex flex-wrap gap-4 text-sm mt-4">
                    {{-- <div class="flex items-center gap-1.5 bg-emerald-700/50 px-3 py-1.5 rounded-md">
                        <flux:icon.identification class="w-4 h-4 text-emerald-300" />
                        <span>NIP. {{ $employee->nip ?? '-' }}</span>
                    </div> --}}
                    <div class="flex items-center gap-1.5 bg-emerald-700/50 px-3 py-1.5 rounded-md">
                        <flux:icon.briefcase class="w-4 h-4 text-emerald-300" />
                        <span>{{ $employee->type }} ({{ $employee->gender == 'l' ? 'Laki-laki' : 'Perempuan' }})</span>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-6! border-l-4 border-l-indigo-500">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-50 text-indigo-600 p-3 rounded-full dark:bg-indigo-900/30">
                    <flux:icon.academic-cap class="w-7 h-7" />
                </div>

                <div>
                    <flux:subheading class="dark:text-white">Pendidikan Terakhir</flux:subheading>

                    <flux:heading size="lg">
                        {{ $employee->education_level ?? '-' }}
                        @if($employee->education_detail)
                            <span class="text-sm font-normal text-zinc-500 dark:text-white">
                                / ({{ $employee->education_detail }})
                            </span>
                        @endif
                    </flux:heading>
                </div>
            </div>
        </flux:card>

        {{-- 2. STATISTIC CARDS --}}
        <div class="grid gap-4 md:grid-cols-3">
            {{-- Masa Kerja --}}
            <flux:card class="flex items-center gap-4 p-6! border-t-4 border-t-emerald-500">
                <div class="bg-emerald-50 text-emerald-600 p-3 rounded-full dark:bg-emerald-900/30">
                    <flux:icon.clock class="w-7 h-7" />
                </div>
                <div>
                    <flux:subheading class="dark:text-white">Masa Kerja</flux:subheading>
                    <flux:heading size="lg">{{ $masaKerja }}</flux:heading>
                </div>
            </flux:card>

            {{-- Pesan / Notifikasi --}}
            <flux:card class="flex items-center gap-4 p-6! border-t-4 border-t-amber-500">
                <div class="bg-amber-50 text-amber-600 p-3 rounded-full dark:bg-amber-900/30">
                    <flux:icon.bell class="w-7 h-7" />
                </div>
                <div>
                    <flux:subheading class="dark:text-white">Pesan Belum Dibaca</flux:subheading>
                    <flux:heading size="lg">{{ $globalUnreadCount ?? 0 }} <span
                            class="text-sm font-normal text-zinc-500 dark:text-white">Pesan</span></flux:heading>
                </div>
            </flux:card>

            {{-- Pintasan Aksi --}}
            <flux:card
                class="flex items-center gap-4 p-6! border-t-4 border-t-blue-500 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition"
                onclick="window.location.href='{{ route('pegawai.notifikasi') }}'">
                <div class="bg-blue-50 text-blue-600 p-3 rounded-full dark:bg-blue-900/30">
                    <flux:icon.envelope-open class="w-7 h-7" />
                </div>
                <div>
                    <flux:subheading class="dark:text-white">Kotak Masuk</flux:subheading>
                    <flux:heading size="md" class="text-blue-600 dark:text-white">Buka Notifikasi &rarr;</flux:heading>
                </div>
            </flux:card>
        </div>

        {{-- 3. NOTIFIKASI / PENGUMUMAN TERBARU --}}
        {{-- <flux:card class="flex-1">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-4">
                <div>
                    <flux:heading size="lg">Notifikasi Sistem Terbaru</flux:heading>
                    <flux:subheading>Peringatan kenaikan pangkat, gaji berkala, dan informasi SIMPEG.</flux:subheading>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($recentNotifs as $notif)
                <div
                    class="flex gap-4 p-4 rounded-lg border {{ $notif->is_read ? 'bg-white border-zinc-100' : 'bg-emerald-50/50 border-emerald-100 shadow-sm' }}">
                    <div class="mt-1">
                        @if(!$notif->is_read)
                        <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></div>
                        @else
                        <div class="w-2.5 h-2.5 bg-zinc-300 rounded-full"></div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <flux:badge size="sm" color="{{ $notif->is_read ? 'zinc' : 'emerald' }}">{{
                                strtoupper($notif->type) }}</flux:badge>
                            <span class="text-xs text-zinc-500">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="font-bold text-sm text-zinc-900">{{ $notif->title }}</h4>
                        <p class="text-sm text-zinc-600 mt-1">{{ \Illuminate\Support\Str::limit($notif->message, 100) }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-zinc-500">
                    <flux:icon.inbox class="w-10 h-10 mx-auto text-zinc-300 mb-2" />
                    Belum ada notifikasi atau pesan masuk untuk Anda.
                </div>
                @endforelse
            </div>

            @if(count($recentNotifs) > 0)
            <div class="mt-6 text-center">
                <flux:button variant="subtle" size="sm" href="{{ route('notifikasi.index') }}" wire:navigate>Lihat Semua
                    Pesan</flux:button>
            </div>
            @endif
        </flux:card> --}}

    </div>
</x-layouts::pegawai_app>