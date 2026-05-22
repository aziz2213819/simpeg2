<x-layouts::app :title="__('Tambah Petugas')">
    <div class="p-6 mx-auto space-y-6">

        
        <flux:card>
            <div class="flex items-center justify-between">
                <div class="mb-6">
                    <flux:heading size="lg" class="text-zinc-700 dark:text-white">Tambah Petugas Baru</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-white">Masukkan data petugas pengangkut sampah dan kendaraan yang digunakan.</p>
                </div>
                <flux:button href="{{ route('workers.index') }}" variant="ghost" icon="arrow-left">
                    Kembali
                </flux:button>
            </div>

            <form action="{{ route('workers.store') }}" method="POST" class="space-y-6">
                @csrf

                <flux:input name="nama_petugas" label="Nama Petugas" placeholder="Masukkan nama petugas..." required />

                <div class="grid sm:grid-cols-2 gap-6">
                    <flux:input name="jenis_kendaraan" label="Jenis Kendaraan" placeholder="Contoh: Truk Sampah, Pick up, Motor Roda 3" required />
                    
                    <flux:input name="plat_nomor" label="Plat Nomor" placeholder="Contoh: M 1234 XX" required />
                </div>

                <flux:select name="tps_id" label="Lokasi TPS Penugasan" placeholder="Pilih TPS..." required>
                    @foreach($tpsList as $tps)
                        <option value="{{ $tps->id }}">{{ $tps->nama_tps }} - Kec. {{ $tps->kecamatan }}</option>
                    @endforeach
                </flux:select>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <flux:button href="{{ route('workers.index') }}" variant="subtle">
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" class="cursor-pointer bg-emerald-600">
                        Simpan Petugas
                    </flux:button>
                </div>
            </form>
        </flux:card>

    </div>
</x-layouts::app>
