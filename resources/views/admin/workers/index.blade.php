<x-layouts::app :title="__('Petugas')">
    <div class="p-6 space-y-6">

        <x-managed-message />

        <flux:card>

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <flux:heading size="lg">Data Petugas</flux:heading>
                    <flux:subheading>Kelola data petugas, kendaraan, dan TPS penugasan.</flux:subheading>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('workers.index') }}" class="flex items-center gap-2">
                        <flux:input name="search" value="{{ request('search') }}" placeholder="Cari Nama / Plat Nomor" icon="magnifying-glass" />
                        <flux:button type="submit" class="cursor-pointer">Cari</flux:button>
                    </form>

                    <flux:button href="{{ route('workers.create') }}">
                        Tambah Petugas
                    </flux:button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Nama Petugas</flux:table.column>
                        <flux:table.column>Jenis Kendaraan</flux:table.column>
                        <flux:table.column>Plat Nomor</flux:table.column>
                        <flux:table.column>TPS Tugas</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($workers as $worker)
                            <flux:table.row>
                                <flux:table.cell>
                                    <span class="font-bold">{{ $worker->nama_petugas }}</span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $worker->jenis_kendaraan }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge>{{ $worker->plat_nomor }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if($worker->tps)
                                        {{ $worker->tps->nama_tps }} ({{ $worker->tps->kecamatan }})
                                    @else
                                        <span class="text-zinc-500 italic">Tidak ada TPS</span>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="flex gap-2">
                                    <flux:button size="sm" href="{{ route('workers.edit', $worker) }}" class="cursor-pointer">
                                        Edit
                                    </flux:button>

                                    <flux:modal.trigger name="delete-worker-{{ $worker->id }}">
                                        <flux:button size="sm" variant="danger" class="cursor-pointer">
                                            Hapus
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-worker-{{ $worker->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Hapus Petugas?</flux:heading>
                                                <flux:subheading>Anda yakin ingin menghapus petugas ini? Tindakan ini tidak dapat dibatalkan.</flux:subheading>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Batal</flux:button>
                                                </flux:modal.close>
                                                <form action="{{ route('workers.destroy', $worker) }}" method="POST">
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
                                    Belum ada data petugas.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="mt-4">
                {{ $workers->appends(['search' => request('search')])->links() }}
            </div>

        </flux:card>

    </div>
</x-layouts::app>
