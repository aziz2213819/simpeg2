<x-layouts::app :title="__('Unggah Foto Struktural')">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <flux:card>
            <div class="flex items-center justify-between mb-6 border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <div>
                    <flux:heading size="lg">Unggah Foto Struktural</flux:heading>
                    <flux:subheading>Pastikan gambar memiliki resolusi yang baik agar jelas saat dibaca.</flux:subheading>
                </div>
                <flux:button href="{{ route('struktural.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                    Kembali
                </flux:button>
            </div>

            {{-- Jangan lupa enctype="multipart/form-data" untuk upload file --}}
            <form action="{{ route('struktural.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">File Foto
                        (Bagan/Struktur)</label>
                    <input type="file" name="photo_path" accept="image/png, image/jpeg, image/jpg" required
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/30 dark:file:text-emerald-400 border border-zinc-300 dark:border-zinc-700 rounded-lg p-2">
                    @error('foto') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- <flux:input name="keterangan" label="Keterangan (Opsional)"
                    placeholder="Contoh: Struktur Organisasi Periode 2026-2030" value="{{ old('keterangan') }}" /> --}}

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button href="{{ route('struktural.index') }}" variant="subtle" wire:navigate>Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 cursor-pointer">Unggah &
                        Simpan</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>