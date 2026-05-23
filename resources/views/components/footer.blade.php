{{-- FOOTER --}}
<footer id="footer" class="bg-zinc-950 text-zinc-400 py-16 px-8">
    <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-12">
        <div class="col-span-2 space-y-6">
            <div class="flex items-center gap-2 text-white">
                <img src="{{ asset('images/logo.png') }}" alt="Logo DLH" class="h-14 w-auto object-contain" />
                <span class="text-2xl font-bold">DLH<span class="text-emerald-500">Care</span></span>
            </div>
            <p class="max-w-sm">Layanan pengaduan masyarakat resmi di bawah naungan Dinas Lingkungan Hidup, khusus untuk
                penanganan persampahan dan kebersihan fasilitas umum.</p>
        </div>
        <div class="space-y-4">
            <h4 class="text-white font-bold">Tautan Cepat</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('pengaduan.create') }}" class="hover:text-emerald-500">Cara Melapor</a></li>
            </ul>
        </div>
        <div class="space-y-4">
            <h4 class="text-white font-bold">Kontak DLH</h4>
            <ul class="space-y-2 text-sm">
                <li class="break-all">pengaduan@dlh.bangkalankab.go.id</li>
                <li>(031) 1234-5678</li>
                <li class="leading-relaxed text-xs">Jl. Soekarno Hatta No.32b, Wr 08, Mlajah, Kec. Bangkalan, Kabupaten
                    Bangkalan, Jawa Timur 69116</li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto border-t border-zinc-800 mt-12 pt-8 text-center text-xs">
        <p>&copy; 2026 Dinas Lingkungan Hidup Kabupaten Bangkalan. Hak Cipta Dilindungi.</p>
    </div>
</footer>