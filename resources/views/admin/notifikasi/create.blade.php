<x-layouts::app :title="__('Kirim Notifikasi')">
    <x-floating-managed-message />
    <div class="p-6 space-y-6">
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="lg" class="text-zinc-700 dark:text-white">Kirim Pesan Notifikasi</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-white">
                        Pesan ini akan langsung muncul di dashboard pegawai yang bersangkutan.
                    </p>
                </div>
                <flux:button href="{{ route('notifikasi.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                    Kembali
                </flux:button>
            </div>

            <form action="{{ route('notifikasi.store') }}" method="POST" class="space-y-6">
                @csrf

                <flux:select name="employee_id" label="Pilih Pegawai Penerima" required searchable>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </flux:select>

                <flux:select name="type" label="Tipe Notifikasi (Kategori)" required>
                    <option value="pangkat" {{ old('type') == 'pangkat' ? 'selected' : '' }}>Kenaikan Pangkat</option>
                    <option value="gaji_berkala" {{ old('type') == 'gaji_berkala' ? 'selected' : '' }}>Kenaikan Gaji
                        Berkala</option>
                    <option value="pensiun" {{ old('type') == 'pensiun' ? 'selected' : '' }}>Persiapan Pensiun</option>
                    </option>
                </flux:select>

                <flux:input name="title" label="Judul Pesan" placeholder="Contoh: Panggilan Penyerahan Berkas"
                    value="{{ old('title') }}" required />

                <flux:textarea name="message" label="Isi Pesan Detail" rows="4"
                    placeholder="Tulis instruksi lengkap di sini..." required>{{ old('message') }}</flux:textarea>

                <div class="flex items-center gap-6">
                    <div>
                        <flux:subheading class="dark:text-white">Butuh Persetujuan (SK)?</flux:subheading>
                        <p class="text-xs text-zinc-500 dark:text-white">Aktifkan jika notifikasi memerlukan upload SK (status: pending)
                        </p>
                    </div>
                    <flux:field variant="inline">
                        <flux:switch name="requires_sk" value="0" {{ old('requires_sk') ? 'checked' : '' }} />
                    </flux:field>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <flux:button href="{{ route('notifikasi.index') }}" variant="subtle" wire:navigate>Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" color="emerald" class="cursor-pointer">Kirim Notifikasi
                        Sekarang</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>