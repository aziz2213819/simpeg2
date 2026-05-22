<x-layouts::app :title="__('Laporan TPS')">
    <div class="p-6 space-y-6">

        <x-managed-message />

        <flux:card>

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <flux:heading size="lg">Laporan Petugas TPS</flux:heading>
                    <flux:subheading>Manajemen laporan pengangkutan sampah dari Petugas Kebersihan.</flux:subheading>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('admin.tps-reports.index') }}" class="flex items-center gap-2">
                        <flux:input name="search" value="{{ request('search') }}" placeholder="Cari Petugas / TPS..." icon="magnifying-glass" />
                        <flux:button type="submit" class="cursor-pointer">Cari</flux:button>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Tanggal</flux:table.column>
                        <flux:table.column>Nama Petugas</flux:table.column>
                        <flux:table.column>TPS Lokasi</flux:table.column>
                        <flux:table.column>Status Angkut</flux:table.column>
                        <flux:table.column>Keterangan</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($reports as $report)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="font-medium">{{ $report->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-zinc-500">{{ $report->created_at->format('H:i') }} WIB</div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="font-bold">{{ $report->worker->nama_petugas ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $report->worker->jenis_kendaraan ?? '-' }} ({{ $report->worker->plat_nomor ?? '-' }})</div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="font-medium">{{ $report->tps->nama_tps ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $report->tps->kecamatan ?? '-' }}</div>
                                    @if($report->tps && $report->tps->lat && $report->tps->lng)
                                        <button class="text-blue-500 hover:underline text-xs flex items-center gap-1 mt-1 cursor-pointer" 
                                            x-data x-on:click="$dispatch('open-map', { lat: {{ $report->tps->lat }}, lng: {{ $report->tps->lng }}, name: '{{ $report->tps->nama_tps }}' })">
                                            <flux:icon.map class="w-3.5 h-3.5" /> Lihat Peta
                                        </button>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if($report->status_angkut === 'sudah')
                                        <flux:badge color="green">Sudah Diangkut</flux:badge>
                                    @else
                                        <flux:badge color="red">Belum Diangkut</flux:badge>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="max-w-[200px] truncate" title="{{ $report->keterangan }}">
                                    {{ $report->keterangan ?: '-' }}
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                    Belum ada laporan TPS.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="mt-4">
                {{ $reports->appends(['search' => request('search')])->links() }}
            </div>

        </flux:card>

    </div>

    {{-- MODAL PETA --}}
    <flux:modal name="map-modal" class="md:w-3/4 max-w-4xl" 
        x-data="{ 
            map: null, 
            marker: null,
            lat: 0,
            lng: 0,
            initMap(lat, lng, name) {
                this.lat = lat;
                this.lng = lng;
                if (!this.map) {
                    this.map = L.map('tps-map', { attributionControl: false }).setView([lat, lng], 15);
                    L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains:['mt0','mt1','mt2','mt3']
                    }).addTo(this.map);
                    this.marker = L.marker([lat, lng]).addTo(this.map);
                } else {
                    this.map.setView([lat, lng], 15);
                    this.marker.setLatLng([lat, lng]);
                }
                this.marker.bindPopup(`<b>${name}</b>`).openPopup();
                
                // Invalidate size when modal opens to fix grey tiles
                setTimeout(() => { this.map.invalidateSize(); }, 300);
            }
        }"
        @open-map.window="$flux.modal('map-modal').show(); initMap($event.detail.lat, $event.detail.lng, $event.detail.name);"
    >
        <div class="p-4">
            <flux:heading size="lg" class="mb-4">Lokasi TPS</flux:heading>
            <div id="tps-map" style="height: 400px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
            <div class="mt-4 flex justify-end gap-2">
                <a x-bind:href="`https://www.google.com/maps/place/${lat},${lng}`" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <flux:icon.map class="w-4 h-4" /> Google Maps
                </a>
                <flux:modal.close>
                    <flux:button variant="ghost">Tutup</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
</x-layouts::app>
