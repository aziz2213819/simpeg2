<x-layouts::app.landing :title="__('Selamat Datang')">
    {{-- Tambahkan Leaflet CSS di sini --}}
    
    <style>
        /* Memastikan pin peta terlihat bagus */
        .custom-pin {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="text-zinc-900 font-sans">
        <x-floating-managed-message />

        <x-navbar />

        {{-- HERO SECTION --}}
        <section class="relative overflow-hidden bg-emerald-50/50 dark:bg-transparent py-20 px-8">
            <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <flux:badge color="green" size="sm">Sistem Layanan Masyarakat Terpadu</flux:badge>
                    <h1 class="text-5xl md:text-6xl font-extrabold leading-tight dark:text-white">
                        Lapor Tumpukan Sampah <br>
                        <span class="text-emerald-600">Lebih Cepat & Mudah.</span>
                    </h1>
                    <p class="text-lg text-zinc-600 max-w-lg dark:text-white">
                        Bantu kami menjaga kebersihan lingkungan dengan melaporkan tumpukan sampah liar, fasilitas
                        kebersihan yang rusak, atau masalah lingkungan di sekitar Anda.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <flux:button href="{{ route('pengaduan.create') }}" variant="primary"
                            class="bg-emerald-600 px-8 dark:text-white" wire:navigate>
                            Buat Laporan Baru
                        </flux:button>
                        <flux:button href="{{ route('tamu.tps') }}" variant="ghost"
                            class="border border-emerald-600 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-zinc-800"
                            wire:navigate>
                            Titik TPS
                        </flux:button>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-emerald-200/50 rounded-full blur-3xl"></div>
                    <div class="relative overflow-hidden aspect-video w-full bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-emerald-100 dark:border-emerald-900/30 flex flex-col">
                        
                        <div class="flex items-center gap-1.5 px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-white/5 z-20">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                        </div>

                        <div class="flex-1 relative z-10 bg-emerald-50/30 dark:bg-emerald-950/10">
                            <div id="hero-map" class="absolute inset-0 w-full h-full"></div>

                            <div id="map-loading" class="absolute inset-0 flex items-center justify-center bg-white dark:bg-zinc-900 pointer-events-none transition-opacity duration-500 z-30">
                                <div class="text-center space-y-3">
                                    <flux:icon.map-pin class="mx-auto text-emerald-600 w-12 h-12 opacity-80 animate-bounce" />
                                    <p class="text-emerald-700/50 dark:text-emerald-400/50 font-medium text-sm tracking-widest uppercase">
                                        Memuat Peta Lokasi...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ALUR TATA CARA --}}
        <section id="fitur" class="py-24 px-8 max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4 dark:text-white">
                <h2 class="text-3xl font-bold">Alur Tata Cara Lapor Sampah</h2>
                <p class="text-zinc-500 dark:text-white max-w-2xl mx-auto">Ikuti langkah-langkah mudah berikut untuk memastikan laporan Anda masuk dan diproses dengan cepat oleh petugas DLH.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl border border-zinc-200 hover:border-emerald-500 transition-all group text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-600 transition-colors">
                        <flux:icon.pencil class="text-emerald-600 group-hover:text-white w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Isi Formulir</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed dark:text-white">Lengkapi data pelapor, lokasi, dan jenis sampah secara jelas agar proses verifikasi berjalan lancar.</p>
                </div>

                <div class="p-8 rounded-2xl border border-zinc-200 hover:border-emerald-500 transition-all group text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-600 transition-colors">
                        <flux:icon.camera class="text-emerald-600 group-hover:text-white w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Unggah Bukti</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed dark:text-white">Lampirkan foto kondisi sampah sebagai bukti agar petugas dapat menindaklanjuti lebih tepat.</p>
                </div>

                <div class="p-8 rounded-2xl border border-zinc-200 hover:border-emerald-500 transition-all group text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-600 transition-colors">
                        <flux:icon.truck class="text-emerald-600 group-hover:text-white w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Pantau Proses</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed dark:text-white">Cek status laporan Anda secara berkala hingga petugas menyelesaikan penanganan.</p>
                </div>
            </div>
        </section>

        {{-- CTA SECTION --}}
        <section class="px-8 mb-20">
            <div class="max-w-7xl mx-auto bg-emerald-600 rounded-3xl p-12 text-center text-white space-y-6">
                <h2 class="text-4xl font-bold">Mari Wujudkan Lingkungan Bersih & Asri!</h2>
                <p class="text-emerald-100 max-w-xl mx-auto">Satu laporan dari Anda sangat berarti untuk mencegah pencemaran dan menjaga keindahan kota kita. Jangan ragu untuk melapor.</p>
                <flux:button href="{{ route('pengaduan.create') }}" variant="subtle" class="bg-white hover:bg-emerald-50 border-none px-10 font-bold" wire:navigate>
                    Lapor Sekarang
                </flux:button>
            </div>
        </section>

        <x-footer />
    </div>

    {{-- Script Leaflet & Inisialisasi --}}
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script>
        document.addEventListener("livewire:navigated", function () {
            const dlhBangkalanLat = -7.0477686; 
            const dlhBangkalanLng = 112.7324695;

            const mapContainer = document.getElementById('hero-map');
            if (!mapContainer || mapContainer._leaflet_id) return;

            const map = L.map('hero-map', {
                center: [dlhBangkalanLat, dlhBangkalanLng],
                zoom: 15,
                scrollWheelZoom: false,
                zoomControl: false,
                attributionControl: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            const customIcon = L.divIcon({
                className: 'custom-pin',
                html: `<div class="flex items-center justify-center w-10 h-10 bg-emerald-600 text-white rounded-full shadow-lg border-2 border-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 40]
            });

            L.marker([dlhBangkalanLat, dlhBangkalanLng], { icon: customIcon })
                .addTo(map)
                .bindPopup("<b>Dinas Lingkungan Hidup</b><br>Kabupaten Bangkalan.");

            map.whenReady(function () {
                setTimeout(() => {
                    const loader = document.getElementById('map-loading');
                    if (loader) {
                        loader.style.opacity = '0';
                        setTimeout(() => loader.remove(), 500);
                    }
                }, 300);
            });
        });
    </script>
</x-layouts::app.landing>