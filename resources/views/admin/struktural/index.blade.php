<x-layouts::app :title="__('Kelola Foto Struktural')">
    <x-managed-message />
    <div class="p-6 space-y-6">

        <flux:card>
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                <div>
                    <flux:heading size="lg">Bagan Struktural Instansi</flux:heading>
                    <flux:subheading>Pilih satu foto yang akan ditampilkan di halaman utama web.</flux:subheading>
                </div>
                <flux:button href="{{ route('struktural.create') }}" icon="plus" wire:navigate>
                    Unggah Foto Baru
                </flux:button>
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Pratinjau Foto</flux:table.column>
                    <flux:table.column>Tanggal Unggah</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($strukturals as $item)
                        <flux:table.row>
                            <flux:table.cell>
                                <a href="{{ asset('storage/' . $item->photo_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $item->photo_path) }}" alt="Struktural"
                                        class="h-16 w-auto rounded border border-zinc-200 dark:border-zinc-700 object-cover hover:opacity-80 transition">
                                </a>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $item->created_at->format('d M Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($item->is_active)
                                    <flux:badge color="green">Ditampilkan di Web</flux:badge>
                                @else
                                    <flux:badge color="zinc">Arsip</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="flex gap-2">

                                {{-- Tombol Jadikan Aktif --}}
                                @if(!$item->is_active)
                                    <form action="{{ route('struktural.activate', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <flux:button type="submit" size="sm" variant="outline"
                                            class="text-emerald-600 hover:bg-emerald-50 cursor-pointer">
                                            Jadikan Utama
                                        </flux:button>
                                    </form>
                                @endif

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('struktural.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus foto ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger"
                                        class="text-emerald-600 hover:bg-emerald-50 cursor-pointer">
                                        Hapus
                                    </flux:button>
                                </form>

                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                Belum ada foto struktural yang diunggah.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>