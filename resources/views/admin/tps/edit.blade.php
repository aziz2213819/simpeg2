<x-layouts::app :title="__('Edit Titik TPS')">
    {{-- 1. CSS ASSETS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css" />
    
    <style>
        #map-edit { 
            height: 450px !important; 
            width: 100% !important; 
            border-radius: 12px;
            border: 2px solid #e4e4e7;
            z-index: 10;
        }
        .leaflet-control-geosearch { z-index: 1000 !important; }
    </style>

    <div class="p-8 max-w-4xl mx-auto space-y-6">

        <flux:card class="p-6">
            <div class="flex items-center justify-between mb-6 border-b border-zinc-100 pb-4">
                <div>
                    <flux:heading size="xl" class="font-bold">Edit Lokasi TPS</flux:heading>
                    <flux:subheading>Ubah posisi pin atau informasi jadwal TPS ini.</flux:subheading>
                </div>
                <flux:button as="a" href="{{ route('admin.tps.index') }}" variant="ghost" icon="arrow-left">Kembali</flux:button>
            </div>
            <form action="{{ route('admin.tps.update', $tps->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama TPS --}}
                    <flux:input label="Nama TPS" name="nama_tps" value="{{ old('nama_tps', $tps->nama_tps) }}" required />

                    {{-- Kecamatan --}}
                    <flux:select label="Kecamatan" name="kecamatan" required>
                        <option value="socah" {{ $tps->kecamatan == 'socah' ? 'selected' : '' }}>Socah</option>
                        <option value="bangkalan" {{ $tps->kecamatan == 'bangkalan' ? 'selected' : '' }}>Bangkalan</option>
                        <option value="kamal" {{ $tps->kecamatan == 'kamal' ? 'selected' : '' }}>Kamal</option>
                    </flux:select>
                </div>

                {{-- Alamat --}}
                <flux:input label="Alamat" name="alamat" id="alamat_input" value="{{ old('alamat', $tps->alamat) }}" placeholder="Alamat otomatis terupdate saat pin digeser..." />

                {{-- Peta Edit --}}
                <div class="space-y-2">
                    <flux:label>Geser Pin untuk Mengubah Lokasi</flux:label>
                    <div id="map-edit"></div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Koordinat (Hidden atau Readonly) --}}
                    <flux:input label="Latitude" name="lat" id="lat" value="{{ old('lat', $tps->lat) }}" readonly />
                    <flux:input label="Longitude" name="lng" id="lng" value="{{ old('lng', $tps->lng) }}" readonly />
                </div>

                <flux:input label="Jadwal Angkut" name="jadwal" value="{{ old('jadwal', $tps->jadwal) }}" />

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                    <flux:button href="{{ route('admin.tps.index') }}" variant="subtle">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white">Simpan Perubahan</flux:button>
                </div>
            </form>
        </flux:card>
    </div>

    {{-- 2. JAVASCRIPT LOGIC --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script src="https://unpkg.com/leaflet-geosearch@3.11.0/dist/bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data posisi lama dari database
            const latAwal = {{ $tps->lat }};
            const lngAwal = {{ $tps->lng }};

            // Init Map
            const map = L.map('map-edit', { attributionControl: false }).setView([latAwal, lngAwal], 17);

            // Layer Satelit
            L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains:['mt0','mt1','mt2','mt3']
            }).addTo(map);

            // Marker yang bisa digeser (Draggable)
            const marker = L.marker([latAwal, lngAwal], {
                draggable: true
            }).addTo(map);

            // Fitur Pencarian Alamat
            const searchControl = new GeoSearch.GeoSearchControl({
                provider: new GeoSearch.OpenStreetMapProvider(),
                style: 'bar',
                showMarker: false,
                searchLabel: 'Cari alamat baru...'
            });
            map.addControl(searchControl);

            // Fungsi Update Data
            function updateData(lat, lng) {
                document.getElementById('lat').value = lat.toFixed(8);
                document.getElementById('lng').value = lng.toFixed(8);

                // Ambil alamat otomatis (Reverse Geocoding)
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.display_name) {
                            document.getElementById('alamat_input').value = data.display_name.substring(0, 150);
                        }
                    });
            }

            // Event: Pin Digeser
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                updateData(pos.lat, pos.lng);
            });

            // Event: Peta Diklik
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateData(e.latlng.lat, e.latlng.lng);
            });

            // Event: Hasil Pencarian
            map.on('geosearch/showlocation', function(e) {
                const results = e.location;
                marker.setLatLng([results.y, results.x]);
                updateData(results.y, results.x);
            });
            
            // Perbaikan tampilan peta jika dalam tab/modal
            setTimeout(() => { map.invalidateSize(); }, 500);
        });
    </script>
</x-layouts::app>