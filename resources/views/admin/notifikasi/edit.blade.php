<x-layouts::app :title="__('Edit Notifikasi')">
    <div class="p-6 space-y-6">
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="lg">Edit Pesan Notifikasi</flux:heading>
                </div>
                <flux:button href="{{ route('notifikasi.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                    Kembali
                </flux:button>
            </div>

            <form action="{{ route('notifikasi.update', $notifikasi->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:select class="mb-0!" name="employee_id" label="Pilih Pegawai Penerima" disabled>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $notifikasi->employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </flux:select>

                <flux:link class="block! mb-4!" :href="route('pegawai.edit', $notifikasi->employee_id)" wire:navigate>
                    {{ __('Lihat Pegawai') }}
                </flux:link>

                <flux:select name="type" label="Tipe Notifikasi (Kategori)" disabled>
                    <option value="pangkat" {{ old('type', $notifikasi->type) == 'pangkat' ? 'selected' : '' }}>Kenaikan
                        Pangkat</option>
                    <option value="gaji_berkala" {{ old('type', $notifikasi->type) == 'gaji_berkala' ? 'selected' : '' }}>
                        Kenaikan Gaji Berkala</option>
                    <option value="pensiun" {{ old('type', $notifikasi->type) == 'pensiun' ? 'selected' : '' }}>Persiapan
                        Pensiun</option>
                </flux:select>

                <flux:input name="title" label="Judul Pesan" value="{{ old('title', $notifikasi->title) }}" disabled />

                <flux:textarea name="message" label="Isi Pesan Detail" rows="4" disabled>
                    {{ old('message', $notifikasi->message) }}
                </flux:textarea>

                {{-- PANEL BARU: Status Dokumen SK dari Pegawai --}}
                @if ($notifikasi->status != null)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <flux:heading size="md" class="mb-3">Lampiran Dokumen (Dari {{ $notifikasi->employee->name }})
                        </flux:heading>

                        @if ($notifikasi->sk_file_path)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-emerald-600 font-medium flex items-center gap-1.5">
                                        <flux:icon.check-circle class="w-5 h-5" /> Berkas telah diunggah
                                    </p>
                                    <p class="text-xs text-zinc-500 mt-1 dark:text-white">Diserahkan pada:
                                        {{ $notifikasi->submitted_at ? $notifikasi->submitted_at->format('d M Y, H:i') : '-' }}
                                        WIB
                                    </p>
                                </div>
                                <flux:button href="{{ asset('storage/' . $notifikasi->sk_file_path) }}" target="_blank"
                                    variant="outline" size="sm"
                                    class="text-emerald-600 hover:text-emerald-700 w-full sm:w-auto">
                                    <flux:icon.eye class="w-4 h-4 mr-2" /> Lihat Dokumen
                                </flux:button>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-sm text-zinc-500">
                                <flux:icon.clock class="w-5 h-5" />
                                <span>Pegawai bersangkutan belum mengunggah berkas balasan untuk notifikasi ini.</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Status Dibaca (Bisa direset oleh Admin) --}}
                <flux:select name="is_read" label="Status Keterbacaan Oleh Pegawai" required>
                    <option value="0" {{ old('is_read', $notifikasi->is_read) == 0 ? 'selected' : '' }}>Belum Dibaca
                    </option>
                    <option value="1" {{ old('is_read', $notifikasi->is_read) == 1 ? 'selected' : '' }}>Sudah Dibaca
                    </option>
                </flux:select>

                <flux:select name="status" label="Kelayakan Berkas" required>
                    <option value="">Belum Ditentukan</option>
                    <option value="pending" {{ old('status', $notifikasi->status) == 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="approved" {{ old('status', $notifikasi->status) == 'approved' ? 'selected' : '' }}>
                        Diterima
                    </option>
                    <option value="rejected" {{ old('status', $notifikasi->status) == 'rejected' ? 'selected' : '' }}>
                        Ditolak
                    </option>

                </flux:select>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button href="{{ route('notifikasi.index') }}" variant="subtle" wire:navigate>Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary"
                        class="bg-emerald-600 hover:bg-emerald-700 cursor-pointer">Simpan
                        Perubahan</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>