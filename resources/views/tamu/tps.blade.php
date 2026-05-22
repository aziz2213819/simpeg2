<x-layouts::app.landing :title="__('Peta Lokasi TPS - DLH Bangkalan')">
    <x-navbar />

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto font-sans">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- KOLOM KIRI: DAFTAR TPS & FILTER (4 Kolom) --}}
            <div class="lg:col-span-5 space-y-4">
                
                {{-- Baris Judul & Filter Sejajar --}}
                <div class="flex items-center justify-between gap-4 mb-2">
                    <h2 class="text-lg font-bold text-zinc-800 dark:text-zinc-100 whitespace-nowrap">Daftar Titik TPS</h2>
                    
                    {{-- Form Filter Mepet Kanan --}}
                    <form action="{{ route('tamu.tps') }}" method="GET" class="flex items-center gap-1">
                        <flux:select name="kecamatan" placeholder="Kecamatan" class="!w-32 !h-9">
                            <option value="">Semua</option>
                            <option value="bangkalan" {{ request('kecamatan') == 'bangkalan' ? 'selected' : '' }}>Bangkalan</option>
                            <option value="socah" {{ request('kecamatan') == 'socah' ? 'selected' : '' }}>Socah</option>
                            <option value="kamal" {{ request('kecamatan') == 'kamal' ? 'selected' : '' }}>Kamal</option>
                        </flux:select>
                        <flux:button type="submit" variant="primary" size="sm" class="!h-9 bg-zinc-900">Cari</flux:button>
                    </form>
                </div>

                {{-- List Card TPS --}}
                <div class="space-y-3 overflow-y-auto max-h-[550px] pr-2 custom-scrollbar">
                    @forelse($all_tps as $tps)
                        <flux:card class="p-4 cursor-pointer hover:ring-2 hover:ring-emerald-500 transition-all shadow-sm border-zinc-200 dark:border-zinc-800 group" 
                                   onclick="focusTPS({{ $tps->lat }}, {{ $tps->lng }})">
                            <div class="flex flex-col">
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-sm font-black text-zinc-900 dark:text-white uppercase group-hover:text-emerald-600">
                                        {{ $tps->nama_tps }}
                                    </h3>
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[9px] font-bold uppercase border border-emerald-100">
                                        {{ $tps->kecamatan }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-zinc-500 italic mb-3 leading-tight">{{ $tps->alamat }}</p>
                                
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-800/80 rounded border-l-4 border-emerald-500">
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase block">Jadwal Angkut:</span>
                                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                        {{ $tps->jadwal ?? 'Belum Tersedia' }}
                                    </span>
                                </div>
                            </div>
                        </flux:card>
                    @empty
                        <div class="text-center py-10 border-2 border-dashed rounded-xl text-zinc-400 text-sm">
                            Data tidak ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- KOLOM KANAN: PETA (7 Kolom) --}}
            <div class="lg:col-span-7">
                <flux:card class="p-1 shadow-2xl ring-1 ring-zinc-200 dark:ring-zinc-800 border-none overflow-hidden">
                    <div id="map-warga" style="width: 100%; height: 600px; border-radius: 10px; z-index: 1;"></div>
                </flux:card>
                <p class="mt-2 text-[10px] text-zinc-400 italic text-right">* Klik marker untuk navigasi Google Maps</p>
            </div>

        </div>
    </div>

    {{-- Script & Leaflet --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>

    <script>
        let mapWarga;
        function initTpsMap() {
            const container = document.getElementById('map-warga');
            if (!container || L.DomUtil.hasClass(container, 'leaflet-container')) return;

            mapWarga = L.map('map-warga', { scrollWheelZoom: false, attributionControl: false }).setView([-7.0454, 112.7441], 13);
            L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0','mt1','mt2','mt3'] }).addTo(mapWarga);

            @foreach($all_tps as $tps)
                @if($tps->lat && $tps->lng)
                    L.marker([{{ $tps->lat }}, {{ $tps->lng }}]).addTo(mapWarga).bindPopup(`
                        <div style="min-width: 150px;">
                            <h4 style="margin:0; color:#10b981; font-size:12px;">{{ strtoupper($tps->nama_tps) }}</h4>
                            <hr style="margin:5px 0; border:0; border-top:1px solid #eee;">
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $tps->lat }},{{ $tps->lng }}" target="_blank" 
                               style="display:block; background:#10b981; color:white; text-align:center; padding:5px; border-radius:4px; text-decoration:none; font-size:10px; font-weight:bold;">
                               NAVIGASI
                            </a>
                        </div>
                    `);
                @endif
            @endforeach
        }

        function focusTPS(lat, lng) {
            if (mapWarga) mapWarga.flyTo([lat, lng], 18, { animate: true, duration: 1.5 });
        }

        document.addEventListener('DOMContentLoaded', initTpsMap);
        document.addEventListener('livewire:navigated', initTpsMap);
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #10b981; }
    </style>
</x-layouts::app.landing>