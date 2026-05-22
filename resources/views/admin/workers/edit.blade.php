<x-layouts::app :title="__('Edit Petugas')">
    <div class="p-6 mx-auto space-y-6">

        <flux:card>
            <div class="flex items-center justify-between">
                <div class="mb-6">
                    <flux:heading size="lg" class="text-zinc-700 dark:text-white">Edit Data Petugas</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-white">Perbarui data petugas pengangkut sampah.</p>
                </div>
                <flux:button href="{{ route('workers.index') }}" variant="ghost" icon="arrow-left">
                    Kembali
                </flux:button>
            </div>

            <form action="{{ route('workers.update', $worker) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:input name="nama_petugas" label="Nama Petugas" value="{{ $worker->nama_petugas }}" required />

                <div class="grid sm:grid-cols-2 gap-6">
                    <flux:input name="jenis_kendaraan" label="Jenis Kendaraan" value="{{ $worker->jenis_kendaraan }}" required />
                    
                    <flux:input name="plat_nomor" label="Plat Nomor" value="{{ $worker->plat_nomor }}" required />
                </div>

                <flux:select name="tps_id" label="Lokasi TPS Penugasan" required>
                    @foreach($tpsList as $tps)
                        <option value="{{ $tps->id }}" {{ $worker->tps_id == $tps->id ? 'selected' : '' }}>
                            {{ $tps->nama_tps }} - Kec. {{ $tps->kecamatan }}
                        </option>
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
