<x-layouts::app :title="__('Manajemen Titik TPS')">
    {{-- 1. CSS ASSETS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css" />
    
    <style>
        #map-picker { 
            height: 400px !important; 
            width: 100% !important; 
            border-radius: 12px;
            border: 2px solid #e4e4e7;
            z-index: 10;
        }
        /* Memastikan kolom pencarian leaflet muncul di atas modal */
        .leaflet-control-geosearch { z-index: 1000 !important; }
        .leaflet-container { cursor: crosshair !important; }
    </style>

    <div class="p-6 space-y-6">
        
        <x-managed-message />

        <flux:card>
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <flux:heading size="lg">Data Titik TPS</flux:heading>
                    <flux:subheading>Kelola lokasi tempat pembuangan sementara dan jadwal pengangkutan.</flux:subheading>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Search Form --}}
                    <form action="{{ route('admin.tps.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                        <flux:select name="kecamatan" onchange="this.form.submit()">
                            <option value="">Semua Kecamatan</option>
                            <option value="socah" {{ request('kecamatan') == 'socah' ? 'selected' : '' }}>Socah</option>
                            <option value="bangkalan" {{ request('kecamatan') == 'bangkalan' ? 'selected' : '' }}>Bangkalan</option>
                            <option value="kamal" {{ request('kecamatan') == 'kamal' ? 'selected' : '' }}>Kamal</option>
                        </flux:select>
                        <flux:input name="search" icon="magnifying-glass" value="{{ request('search') }}" placeholder="Cari nama TPS..." />
                        <flux:button type="submit" class="cursor-pointer">Cari</flux:button>
                    </form>

                    {{-- Tombol Tambah --}}
                    <flux:modal.trigger name="create-tps">
                        <flux:button icon="plus" class="cursor-pointer" onclick="startMapProcess()">Tambah TPS</flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Table --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nama TPS & Lokasi</flux:table.column>
                    <flux:table.column>Kecamatan</flux:table.column>
                    <flux:table.column>Jadwal Angkut</flux:table.column>
                    <flux:table.column>Peta</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($tps_data as $tps)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-bold">{{ $tps->nama_tps }}</div>
                                <div class="text-xs text-zinc-500 mt-0.5">{{ $tps->alamat }}</div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge>{{ ucfirst($tps->kecamatan) }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $tps->jadwal }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <button class="text-blue-500 hover:underline text-xs flex items-center gap-1 mt-1 cursor-pointer" 
                                    x-data x-on:click="$dispatch('open-preview-map', { lat: {{ $tps->lat }}, lng: {{ $tps->lng }}, name: '{{ $tps->nama_tps }}' })">
                                    <flux:icon.map class="w-3.5 h-3.5" /> Lihat Peta
                                </button>
                            </flux:table.cell>

                            <flux:table.cell class="flex gap-2">
                                <flux:button size="sm" href="{{ route('admin.tps.edit', $tps->id) }}" class="cursor-pointer">
                                    Edit
                                </flux:button>

                                <flux:modal.trigger name="delete-tps-{{ $tps->id }}">
                                    <flux:button size="sm" variant="danger" class="cursor-pointer">
                                        Hapus
                                    </flux:button>
                                </flux:modal.trigger>

                                <flux:modal name="delete-tps-{{ $tps->id }}" class="min-w-[22rem]">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">Hapus TPS?</flux:heading>
                                            <flux:subheading>Anda yakin ingin menghapus data TPS ini? Tindakan ini tidak dapat dibatalkan.</flux:subheading>
                                        </div>
                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">Batal</flux:button>
                                            </flux:modal.close>
                                            <form action="{{ route('admin.tps.destroy', $tps->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" variant="danger">Hapus</flux:button>
                                            </form>
                                        </div>
                                    </div>
                                </flux:modal>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                Belum ada data TPS.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $tps_data->links() }}
            </div>
        </flux:card>
    </div>

    {{-- MODAL PETA PREVIEW --}}
    <flux:modal name="preview-map-modal" class="md:w-3/4 max-w-4xl" 
        x-data="{ 
            mapPreview: null, 
            markerPreview: null,
            lat: 0,
            lng: 0,
            initPreviewMap(lat, lng, name) {
                this.lat = lat;
                this.lng = lng;
                if (!this.mapPreview) {
                    this.mapPreview = L.map('tps-preview-map', { attributionControl: false }).setView([lat, lng], 15);
                    L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains:['mt0','mt1','mt2','mt3']
                    }).addTo(this.mapPreview);
                    this.markerPreview = L.marker([lat, lng]).addTo(this.mapPreview);
                } else {
                    this.mapPreview.setView([lat, lng], 15);
                    this.markerPreview.setLatLng([lat, lng]);
                }
                this.markerPreview.bindPopup(`<b>${name}</b>`).openPopup();
                
                // Invalidate size when modal opens to fix grey tiles
                setTimeout(() => { this.mapPreview.invalidateSize(); }, 300);
            }
        }"
        @open-preview-map.window="$flux.modal('preview-map-modal').show(); initPreviewMap($event.detail.lat, $event.detail.lng, $event.detail.name);"
    >
        <div class="p-4">
            <flux:heading size="lg" class="mb-4">Lokasi TPS</flux:heading>
            <div id="tps-preview-map" style="height: 400px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
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

    {{-- MODAL TAMBAH TPS --}}
    <flux:modal name="create-tps" class="max-w-xl">
        <form action="{{ route('admin.tps.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <flux:heading size="lg">Tambah Titik TPS Baru</flux:heading>
                <flux:subheading>Cari nama jalan atau geser pin di peta satelit.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Nama TPS" name="nama_tps" placeholder="Contoh: TPS Depo Brok" required />
                <flux:input label="Alamat" name="alamat" id="alamat_input" placeholder="Alamat otomatis terisi saat pilih peta..." />
                
                {{-- AREA PETA GOOGLE SATELLITE STYLE --}}
                <div class="space-y-2">
                    <flux:label>Pilih Lokasi TPS</flux:label>
                    <div id="map-picker"></div>
                </div>

                {{-- Hidden input koordinat --}}
                <input type="hidden" name="lat" id="lat" value="-7.0454">
                <input type="hidden" name="lng" id="lng" value="112.7441">

                <div class="grid grid-cols-2 gap-4">
                    <flux:select label="Kecamatan" name="kecamatan" required>
                        <option value="socah">Socah</option>
                        <option value="bangkalan">Bangkalan</option>
                        <option value="kamal">Kamal</option>
                    </flux:select>
                    <flux:input label="Jadwal Angkut" name="jadwal" placeholder="Senin & Kamis (08:00)" />
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <flux:modal.close class="flex-1">
                    <flux:button variant="ghost" class="w-full">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="flex-1 bg-emerald-600">Simpan Lokasi TPS</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- 3. JAVASCRIPT LOGIC --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script src="https://unpkg.com/leaflet-geosearch@3.11.0/dist/bundle.min.js"></script>

    <script>
        let map, marker;

        function initMapSystem() {
            // Jika peta sudah ada, kita hapus agar tidak error saat buka ulang modal
            if (map != undefined) { map.remove(); }

            // Fokus ke Bangkalan
            map = L.map('map-picker', { attributionControl: false }).setView([-7.045484, 112.744116], 16);

            // PAKAI LAYER GOOGLE MAPS HYBRID (Satelit + Jalan)
            // Biar admin bisa liat bangunan/tong sampah asli dari langit
            L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains:['mt0','mt1','mt2','mt3']
            }).addTo(map);

            // Marker Merah Utama
            marker = L.marker([-7.045484, 112.744116], {
                draggable: true
            }).addTo(map);

            // Tambahkan Fitur Cari Alamat/Jalan
            const searchControl = new GeoSearch.GeoSearchControl({
                provider: new GeoSearch.OpenStreetMapProvider(),
                style: 'bar',
                showMarker: false,
                autoClose: true,
                searchLabel: 'Cari nama jalan/lokasi...'
            });
            map.addControl(searchControl);

            // Fungsi Update Input & Ambil Alamat Otomatis
            function updateData(lat, lng) {
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;

                // Ambil alamat dari koordinat (Reverse Geocoding)
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.display_name) {
                            document.getElementById('alamat_input').value = data.display_name.substring(0, 90);
                        }
                    });
            }

            // Event: Pin Digeser
            marker.on('dragend', function(e) {
                let pos = marker.getLatLng();
                updateData(pos.lat, pos.lng);
            });

            // Event: Peta Diklik
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateData(e.latlng.lat, e.latlng.lng);
            });

            // Event: Hasil Pencarian Dipilih
            map.on('geosearch/showlocation', function(e) {
                const results = e.location;
                marker.setLatLng([results.y, results.x]);
                updateData(results.y, results.x);
            });

            // Paksa refresh ukuran peta agar tidak abu-abu
            setTimeout(() => { map.invalidateSize(); }, 300);
        }

        // Fungsi yang dipanggil saat tombol "Tambah TPS" diklik
        function startMapProcess() {
            setTimeout(() => {
                initMapSystem();
            }, 400); // Delay agar modal selesai muncul dulu
        }
    </script>
</x-layouts::app>