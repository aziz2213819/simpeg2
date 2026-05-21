<x-layouts::app.landing :title="__('Portal Laporan - DLH Bangkalan')">
    <x-navbar />
    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto font-sans">

        {{-- Header Portal --}}
        <div class="text-center mb-12">
            <x-heading-dlh />
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mt-4">Portal Transparansi Laporan</h1>
            <p class="mt-3 text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto text-base italic">
                "Daftar aspirasi dan laporan kebersihan masyarakat wilayah Bangkalan yang sedang diproses petugas."
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Bagian Kiri: Daftar Laporan --}}
            <div class="lg:col-span-2 space-y-6">
                
                <div class="flex justify-between items-center mb-2 px-1">
                    <h3 class="font-bold text-zinc-800 dark:text-white flex items-center gap-2 uppercase text-sm tracking-widest">
                        <flux:icon.chat-bubble-left-right class="w-5 h-5 text-emerald-500" /> Laporan Terbaru
                    </h3>
                    <flux:button href="{{ route('pengaduan.create') }}" size="sm" variant="primary" class="bg-amber-500 hover:bg-amber-600 border-none font-bold text-xs shadow-sm" wire:navigate>
                        + BUAT PENGADUAN
                    </flux:button>
                </div>

                @forelse($allReports as $item)
                <div onclick="window.location.href='{{ route('pengaduan.show', $item->id) }}'" class="cursor-pointer">
                    <flux:card class="p-0 overflow-hidden border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        
                        {{-- Meta Info --}}
                        <div class="bg-zinc-100/80 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 px-5 py-2.5 flex justify-between items-center text-xs">
                            <span class="font-black text-emerald-700 dark:text-emerald-500 flex items-center gap-1.5 uppercase tracking-[0.15em]">
                                <flux:icon.megaphone class="w-3.5 h-3.5"/> Pengaduan
                            </span>
                            <span class="text-zinc-500 dark:text-white font-medium flex items-center gap-1">
                                <flux:icon.clock class="w-3.5 h-3.5"/> {{ $item->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="p-5">
                            {{-- Profil Pelapor --}}
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold shadow-inner">
                                    {{ substr($item->nama_pelapor ?? 'N', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-zinc-900 dark:text-white">
                                        {{ Str::mask($item->nama_pelapor ?? 'Masyarakat', '*', 2) }}
                                    </h4>
                                    <p class="text-sm text-zinc-400 uppercase font-bold tracking-tighter">Warga Bangkalan</p>
                                </div>
                            </div>
                            
                            {{-- Deskripsi --}}
                            <div class="mb-5 px-1">
                                <p class="text-zinc-700 dark:text-zinc-300 text-base leading-relaxed italic">
                                    "{{ $item->deskripsi }}"
                                </p>
                            </div>
                            
                            {{-- Box Informasi & Foto --}}
                            <div class="bg-zinc-50 dark:bg-zinc-900/30 rounded-xl p-3 border border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row gap-4">
                                {{-- Container Foto --}}
                                <div class="flex-none mx-auto md:mx-0" style="width: 128px; height: 128px;">
                                    <div class="relative w-full h-full rounded-lg overflow-hidden border border-zinc-300 bg-zinc-200 flex items-center justify-center">
                                        
                                        @php
                                            // Cek keberadaan file di storage via PHP
                                            $path = $item->foto_bukti;
                                            $file_exists = $path && Storage::disk('public')->exists($path);
                                        @endphp

                                        @if($file_exists)
                                            <a href="{{ Storage::url($path) }}" target="_blank" class="w-full h-full">
                                                <img src="{{ Storage::url($path) }}" 
                                                    class="w-full h-full object-cover"
                                                    alt="Bukti Laporan">
                                            </a>
                                        @else
                                            <div class="flex flex-col items-center justify-center text-center p-2">
                                                <img src="{{ asset('images/placeholder.png') }}" class="w-10 h-10 opacity-30 mb-1" />
                                                <span class="text-zinc-400 text-[8px] font-black uppercase tracking-tighter">GAMBAR TIDAK ADA</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Detail Spesifik --}}
                                <div class="flex-1 space-y-2 py-1">
                                    <span class="inline-block px-2 py-1 bg-blue-600 text-white text-xs font-black rounded uppercase tracking-wider mb-1">
                                        Informasi Detail
                                    </span>
                                    <div class="text-sm space-y-2 text-zinc-600 dark:text-zinc-400 font-medium">
                                        <div class="flex items-center gap-2">
                                            <flux:icon.tag class="w-3.5 h-3.5 text-zinc-400"/>
                                            <span>Jenis: <span class="text-zinc-900 dark:text-white font-bold">{{ Str::headline($item->tipe_sampah) }}</span></span>
                                        </div>
                                        <div class="flex items-start gap-2 leading-tight">
                                            <flux:icon.map-pin class="w-3.5 h-3.5 text-zinc-400 shrink-0 mt-0.5"/>
                                            <span class="line-clamp-2">Lokasi: <span class="text-zinc-900 dark:text-white font-bold">{{ $item->lokasi_manual ?? 'Area Bangkalan' }}</span></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <flux:icon.check-circle class="w-3.5 h-3.5 {{ $item->status === 'selesai' ? 'text-emerald-500' : 'text-blue-500' }}"/>
                                            <span class="font-bold uppercase tracking-tighter {{ $item->status === 'selesai' ? 'text-emerald-600' : 'text-blue-600' }}">
                                                {{ $item->status === 'selesai' ? 'Tervalidasi Selesai' : 'Terverifikasi Sistem' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Card --}}
                            <div class="flex justify-between items-center pt-4 border-t border-zinc-100 dark:border-zinc-800 mt-3">
                                {{-- Tombol Share --}}
                                <flux:button 
                                    variant="subtle" 
                                    size="xs" 
                                    icon="share" 
                                    class="text-zinc-400 border shadow-none px-3"
                                    onclick="copyShareLink('{{ route('pengaduan.show', $item->id) }}')"
                                >
                                    Bagikan
                                </flux:button>

                                <div class="flex items-center gap-1.5">
                                    {{-- Status Badge --}}
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter shadow-sm
                                        {{ $item->status === 'selesai' ? 'bg-emerald-500 text-white' : ($item->status === 'proses' ? 'bg-blue-500 text-white' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-800') }}">
                                        {{ $item->status === 'selesai' ? 'Selesai' : ($item->status === 'proses' ? 'Diproses' : 'Menunggu') }}
                                    </span>

                                    {{-- Tombol Komentar --}}
                                    <a href="{{ route('pengaduan.show', $item->id) }}#comments" class="no-underline" wire:navigate>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter bg-orange-500 text-white shadow-sm hover:bg-orange-600 transition-colors cursor-pointer flex items-center gap-2">
                                            <span class="bg-white/20 px-1 py-0.5 rounded text-[9px]">
                                                {{ $item->comments_count ?? 0 }}
                                            </span>
                                            Komentar
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </flux:card>
                </div>
                @empty
                    <div class="text-center py-20 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl">
                        <flux:icon.inbox class="w-12 h-12 mx-auto text-zinc-300 mb-4" />
                        <p class="text-zinc-500 text-sm italic">Belum ada laporan publik yang masuk hari ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Bagian Kanan: Alur Kerja --}}
            <div class="space-y-6">
                <flux:card class="p-6 border-zinc-200 shadow-sm border-t-4 border-t-emerald-600 sticky top-6">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-[11px] mb-6 flex items-center gap-2 border-b pb-3 uppercase tracking-widest">
                        <flux:icon.information-circle class="w-4 h-4 text-emerald-500" /> Tahapan Penanganan
                    </h3>
                    <div class="space-y-6">
                        @php
                            $steps = [
                                ['Laporan Masuk', 'Warga mengirimkan pengaduan melalui sistem.'],
                                ['Verifikasi Admin', 'Tim DLH memvalidasi lokasi dan jenis laporan.'],
                                ['Petugas Meluncur', 'Armada kebersihan dikerahkan ke titik lokasi.']
                            ];
                        @endphp
                        @foreach($steps as $index => $step)
                        <div class="flex gap-4 relative">
                            @if(!$loop->last)
                                <div class="absolute left-3 top-6 w-0.5 h-10 bg-zinc-100 dark:bg-zinc-800"></div>
                            @endif
                            <div class="flex-none w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black z-10 shadow-sm">{{ $index + 1 }}</div>
                            <div class="text-xs">
                                <span class="font-bold text-zinc-900 dark:text-white block uppercase mb-0.5">{{ $step[0] }}</span>
                                <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed">{{ $step[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
    <x-footer />

    <script>
        function copyShareLink(path) {
            navigator.clipboard.writeText(path).then(() => {
                alert('Link laporan berhasil disalin!');
            }).catch(err => {
                alert('Gagal menyalin link.');
            });
        }
    </script>
</x-layouts::app.landing>