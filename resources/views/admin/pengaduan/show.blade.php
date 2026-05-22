<x-layouts::app.landing :title="__('Detail Laporan - DLH Bangkalan')">
    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto font-sans">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <flux:button href="{{ route('cek.status') }}" variant="subtle" icon="arrow-left" size="sm">Kembali</flux:button>
            {{-- ID Laporan sudah dihapus dari sini --}}
        </div>

        <flux:card class="p-0 overflow-hidden border-zinc-200 shadow-lg">
            {{-- Header Card --}}
            <div class="border-b border-zinc-200 px-6 py-4 flex justify-between items-center">
                <span
                    class="font-black text-emerald-700 dark:text-white flex items-center gap-2 uppercase text-xs tracking-widest">
                    <flux:icon.megaphone class="w-4 h-4" /> Detail Pengaduan Masyarakat
                </span>
                <span class="text-xs font-medium">{{ $item->created_at->format('d M Y, H:i') }}</span>
            </div>

            <div class="p-6">
                {{-- Profil Pelapor --}}
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                        {{ substr($item->nama_pelapor ?? 'N', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white">{{ $item->nama_pelapor ?? 'Masyarakat' }}
                        </h4>
                        <p class="text-xs text-zinc-400 dark:text-white uppercase font-bold">Warga Bangkalan</p>
                    </div>
                </div>

                {{-- Konten Laporan --}}
                <div class="mb-8">
                    <p class="text-zinc-700 dark:text-white text-lg leading-relaxed italic mb-6">
                        "{{ $item->deskripsi }}"
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 rounded-2xl p-5 border border-zinc-100">
                        {{-- Foto Bukti --}}
                        <div class="relative aspect-square rounded-xl overflow-hidden border border-zinc-300">
                            @if($item->foto_bukti)
                                <img src="{{ Storage::url($item->foto_bukti) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-zinc-400 font-bold">TANPA FOTO
                                </div>
                            @endif
                        </div>

                        {{-- Info Detail --}}
                        <div class="space-y-4">
                            <div>
                                <span
                                    class="block text-[10px] font-black text-zinc-400 dark:text-white uppercase mb-1">Jenis
                                    Sampah</span>
                                <span
                                    class="px-3 py-1 border border-zinc-200 rounded-lg text-sm font-bold text-zinc-800 dark:text-white">
                                    {{ Str::headline($item->tipe_sampah) }}
                                </span>
                            </div>
                            <div>
                                <span
                                    class="block text-[10px] font-black text-zinc-400 dark:text-white uppercase mb-1">Lokasi
                                    Kejadian</span>
                                <div
                                    class="flex items-start gap-2 text-zinc-800 dark:text-white font-bold text-sm leading-tight">
                                    <flux:icon.map-pin class="w-4 h-4 text-emerald-500 shrink-0" />
                                    {{ $item->lokasi_manual ?? 'Area Bangkalan' }}
                                </div>
                            </div>
                            <div>
                                <span
                                    class="block text-[10px] font-black text-zinc-400 dark:text-white uppercase mb-1">Status
                                    Saat Ini</span>
                                <span
                                    class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-tighter shadow-sm
                                    {{ $item->status === 'selesai' ? 'bg-emerald-500 text-white' : ($item->status === 'proses' ? 'bg-blue-500 text-white' : 'bg-zinc-200 text-zinc-500') }}">
                                    {{ $item->status === 'selesai' ? 'Selesai' : ($item->status === 'proses' ? 'Sedang Diproses' : 'Menunggu Verifikasi') }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs font-black text-zinc-400 dark:text-white uppercase mb-2">
                                    Lokasi di Peta
                                </span>

                                <div id="map" class="w-full h-40 rounded-xl border border-zinc-200"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-zinc-100 mb-8">

                {{-- ========================================== --}}
                {{-- BAGIAN KOMENTAR (TANGGAPAN PETUGAS) --}}
                {{-- ========================================== --}}
                <div id="comments" class="space-y-6">
                    <h3
                        class="font-bold text-zinc-900 dark:text-white flex items-center gap-2 uppercase text-sm tracking-widest mb-4">
                        <flux:icon.chat-bubble-left-right class="w-5 h-5" />
                        <span class="text-emerald-500 dark:text-white">
                            Tanggapan Petugas DLH
                        </span>
                    </h3>

                    {{-- List Komentar --}}
                    <div class="space-y-4">
                        @forelse($item->comments as $comment)
                            <div
                                class="p-5 rounded-2xl border {{ $comment->user->is_admin_sampah ? 'bg-emerald-50 border-emerald-100' : 'bg-zinc-50 border-zinc-100' }}">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-black text-emerald-800 uppercase tracking-widest">
                                        {{ $comment->user->name ?? 'Admin DLH Bangkalan' }}
                                    </span>
                                    <span
                                        class="text-[14px] text-zinc-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-zinc-700 leading-relaxed">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <div class="text-center py-10 rounded-2xl border border-dashed">
                                <p class="text-zinc-400 dark:text-white text-sm italic">Belum ada tanggapan resmi untuk
                                    laporan ini.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Form Input (Hanya Muncul Untuk Admin Sampah) --}}
                    @auth
                        @if(auth()->user()->is_admin_sampah)
                            <div class="mt-8 p-6 border-2 border-emerald-500/20 rounded-2xl shadow-sm">
                                <h4 class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-4 italic">Kirim
                                    Tanggapan Sebagai Admin</h4>
                                <form action="{{ route('admin.pengaduan.comment', $item->id) }}" method="POST">
                                    @csrf
                                    <textarea name="body" required rows="3"
                                        class="w-full bg-zinc-50 border-zinc-200 rounded-xl p-4 text-sm focus:ring-emerald-500 focus:border-emerald-500 mb-3"
                                        placeholder="Berikan instruksi atau update progress penanganan sampah..."></textarea>

                                    <div class="flex justify-end">
                                        <flux:button type="submit" variant="primary"
                                            class="bg-emerald-600 hover:bg-emerald-700 font-bold px-8">
                                            KIRIM UPDATE
                                        </flux:button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
                {{-- ========================================== --}}

            </div>
        </flux:card>
    </div>
    <x-footer />
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const lat = {{ $item->latitude ?? -7.05 }};
            const lng = {{ $item->longitude ?? 112.75 }};

            // 🔴 fallback kalau null
            if (!lat || !lng) return;

            const map = L.map('map', {
                center: [lat, lng],
                zoom: 15,
                attributionControl: false,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: ''
            }).addTo(map);

            // marker
            L.marker([lat, lng]).addTo(map)
                .bindPopup("Lokasi Laporan")
                .openPopup();
        });
    </script>
</x-layouts::app.landing>