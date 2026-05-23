<x-layouts::app :title="__('Notifikasi')">

    <x-managed-message />
    
    <div class="p-6 space-y-6">

        <flux:card>
            {{-- Header & Search Form --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                <div>
                    <flux:heading size="lg">Data Notifikasi Sistem</flux:heading>
                    <flux:subheading>Kelola data notifikasi</flux:subheading>
                </div>

                {{-- Wrapper Kanan: Berubah jadi tumpuk di HP, berjajar di Layar Besar --}}
                <div class="flex flex-col sm:flex-row w-full lg:w-auto items-start sm:items-center gap-3">

                    <form action="{{ route('notifikasi.index') }}" method="GET"
                        class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">

                        {{-- Dropdown --}}
                        <div class="w-full sm:w-40">
                            <flux:select name="type" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                <option value="pangkat" {{ request('type') == 'pangkat' ? 'selected' : '' }}>Kenaikan
                                    Pangkat</option>
                                <option value="gaji_berkala" {{ request('type') == 'gaji_berkala' ? 'selected' : '' }}>
                                    Gaji Berkala</option>
                                <option value="pensiun" {{ request('type') == 'pensiun' ? 'selected' : '' }}>Pensiun
                                </option>
                            </flux:select>
                        </div>

                        {{-- Input Search & Tombol Cari --}}
                        <div class="flex w-full sm:w-auto gap-2">
                            <flux:input name="search" type="search" value="{{ request('search') }}"
                                placeholder="Cari Nama atau Judul..." class="w-full sm:w-56" />
                            <flux:button type="submit">Cari</flux:button>
                        </div>

                    </form>

                    {{-- Tombol Tambah Manual --}}
                    <flux:button href="{{ route('notifikasi.create') }}" class="w-full sm:w-auto"
                        wire:navigate>
                        Kirim Manual
                    </flux:button>

                </div>
            </div>

            {{-- Table --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Penerima</flux:table.column>
                    <flux:table.column>Tipe</flux:table.column>
                    <flux:table.column>Judul Pesan</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($notifications as $notif)
                        <flux:table.row>
                            <flux:table.cell>
                                <b>{{ $notif->employee->name ?? 'Pegawai Dihapus' }}</b>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge color="zinc">{{ Str::headline($notif->type) }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>{{ Str::limit($notif->title, 20) }}</flux:table.cell>

                            <flux:table.cell>
                                @if($notif->is_read)
                                    <flux:badge color="green">Dibaca</flux:badge>
                                @else
                                    <flux:badge color="red">Belum Dibaca</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="flex justify-end gap-2">
                                @if($notif->type === 'pangkat' && is_null($notif->status))
                                    <flux:modal.trigger name="send-notif-{{ $notif->id }}">
                                        <flux:button size="sm" color="blue" class="cursor-pointer">
                                            ✈ Kirim
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal name="send-notif-{{ $notif->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Kirim Notifikasi?</flux:heading>
                                                <flux:subheading>Anda yakin ingin mengirim notifikasi ini ke pegawai sekarang?</flux:subheading>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Batal</flux:button>
                                                </flux:modal.close>
                                                <form action="{{ route('notifikasi.send', $notif->id) }}" method="POST">
                                                    @csrf
                                                    <flux:button type="submit" color="blue">Kirim</flux:button>
                                                </form>
                                            </div>
                                        </div>
                                    </flux:modal>
                                @endif
                                <flux:button size="sm" href="{{ route('notifikasi.edit', $notif->id) }}" wire:navigate>
                                    Edit
                                </flux:button>

                                <flux:modal.trigger name="delete-notif-{{ $notif->id }}">
                                    <flux:button size="sm" variant="danger" class="cursor-pointer">Hapus</flux:button>
                                </flux:modal.trigger>

                                {{-- Modal Konfirmasi Hapus --}}
                                <flux:modal name="delete-notif-{{ $notif->id }}" class="min-w-88">
                                    <form action="{{ route('notifikasi.destroy', $notif->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Hapus Notifikasi?</flux:heading>
                                                <flux:subheading>Tindakan ini akan menarik kembali pesan yang sudah dikirim
                                                    ke pegawai ini.</flux:subheading>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Batal</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="danger" class="cursor-pointer">Ya, Hapus</flux:button>
                                            </div>
                                        </div>
                                    </form>
                                </flux:modal>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-4 text-gray-500">
                                @if($search)
                                    Tidak menemukan notifikasi dengan kata kunci "<b>{{ $search }}</b>".
                                @else
                                    Belum ada notifikasi yang terkirim.
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </flux:card>
    </div>
</x-layouts::app>