<x-layouts::petugas_app :title="__('Form Laporan TPS')">
    <div class="p-6 mx-auto space-y-6">

        <x-managed-message />

        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="lg">Form Laporan Pengangkutan Sampah</flux:heading>
                    <flux:subheading>Laporkan status pengangkutan sampah di lokasi TPS penugasan Anda.</flux:subheading>
                </div>
                <flux:button href="{{ route('petugas.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>
                    Kembali
                </flux:button>
            </div>

            <form action="{{ route('petugas.laporan.store') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Data Petugas & TPS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-zinc-50 dark:bg-zinc-800 p-4 rounded-xl border border-zinc-100">
                    <div>
                        <span class="text-xs text-zinc-500 block mb-1">Nama Petugas</span>
                        <div class="font-medium text-sm">{{ $worker->nama_petugas ?? auth()->user()->name }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 block mb-1">Lokasi TPS</span>
                        <div class="font-medium text-sm">{{ $worker->tps->nama_tps ?? 'Belum ada TPS' }}</div>
                    </div>
                </div>

                @if($worker->tps && $worker->tps->lat && $worker->tps->lng)
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm"
                     x-data="{ 
                        initMap() {
                            const map = L.map('petugas-tps-map', { attributionControl: false }).setView([{{ $worker->tps->lat }}, {{ $worker->tps->lng }}], 16);
                            L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                                maxZoom: 20,
                                subdomains:['mt0','mt1','mt2','mt3']
                            }).addTo(map);
                            L.marker([{{ $worker->tps->lat }}, {{ $worker->tps->lng }}])
                             .addTo(map)
                             .bindPopup(`<b>{{ $worker->tps->nama_tps }}</b>`)
                             .openPopup();
                             
                             setTimeout(() => { map.invalidateSize(); }, 300);
                        }
                     }"
                     x-init="initMap()"
                >
                    <div id="petugas-tps-map" style="height: 250px; width: 100%; z-index: 1;"></div>
                    <div class="p-2 bg-zinc-50 dark:bg-zinc-800/50 flex justify-end">
                        <a href="https://www.google.com/maps/place/{{ $worker->tps->lat }},{{ $worker->tps->lng }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50">
                            <flux:icon.map class="w-3.5 h-3.5" /> Buka di Google Maps
                        </a>
                    </div>
                </div>
                @endif

                <div class="space-y-4">
                    <flux:radio.group label="Status Pengangkutan" name="status_angkut" required>
                        <flux:radio value="sudah" label="Sudah Diangkut" description="Sampah di lokasi TPS telah dibersihkan." />
                        <flux:radio value="belum" label="Belum Diangkut" description="Masih terdapat penumpukan sampah." />
                    </flux:radio.group>

                    <flux:textarea label="Keterangan (Opsional)" name="keterangan" placeholder="Masukkan catatan tambahan jika ada..." />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                    <flux:button href="{{ route('petugas.dashboard') }}" variant="subtle">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white">Kirim Laporan</flux:button>
                </div>
            </form>
        </flux:card>

    </div>
    
</x-layouts::petugas_app>
