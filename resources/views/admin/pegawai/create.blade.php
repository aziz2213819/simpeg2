<x-layouts::app :title="__('Tambah Pegawai')">
    <div class="p-6 space-y-6">

        <flux:card>
            {{-- Header Form --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="lg" class="text-zinc-700 dark:text-white">Form Tambah Pegawai</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-white">Masukkan data biodata dan informasi jabatan pegawai
                        baru.</p>
                </div>
                <flux:button href="{{ route('pegawai.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                    Kembali
                </flux:button>
            </div>

            {{-- Form Start --}}
            <form action="{{ route('pegawai.store') }}" method="POST" class="space-y-8">
                @csrf

                <fieldset>
                    <legend class="text-sm font-semibold text-zinc-700 dark:text-white mb-4 border-b pb-2 w-full">1.
                        Biodata Pribadi
                    </legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- <flux:input name="nip" label="NIP" placeholder="Masukkan 18 digit NIP" value="{{ old('nip') }}"
                            minlength="18" maxlength="18" required /> --}}

                        <flux:input name="name" label="Nama Lengkap (Beserta Gelar)" placeholder="Budi Santoso, S.Kom."
                            value="{{ old('name') }}" required />

                        <flux:input type="date" name="birth_date" label="Tanggal Lahir" value="{{ old('birth_date') }}"
                            required />

                        <flux:select name="gender" label="Jenis Kelamin" required>
                            <option value="">Pilih Jenis Kelamin...</option>
                            <option value="l" {{ old('gender') == 'l' ? 'selected' : '' }}>Laki-Laki
                            </option>
                            <option value="p" {{ old('gender') == 'p' ? 'selected' : '' }}>Perempuan
                            </option>
                        </flux:select>

                        <flux:select name="education_level" label="Jenjang Pendidikan Terakhir" required>
                            <option value="">-- Pilih Jenjang --</option>

                            <option value="SD" {{ old('education_level') == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('education_level') == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('education_level') == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D1" {{ old('education_level') == 'D1' ? 'selected' : '' }}>D1</option>
                            <option value="D2" {{ old('education_level') == 'D2' ? 'selected' : '' }}>D2</option>
                            <option value="D3" {{ old('education_level') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="D4" {{ old('education_level') == 'D4' ? 'selected' : '' }}>D4</option>
                            <option value="S1" {{ old('education_level') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('education_level') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('education_level') == 'S3' ? 'selected' : '' }}>S3</option>
                        </flux:select>

                        <flux:input type="text" name="education_detail" label="Detail Pendidikan"
                            value="{{ old('education_detail') }}" placeholder="Contoh: IPS / Teknik / Manajemen" required />
                    </div>
                </fieldset>

                {{-- SECTION 2: Status & Jabatan --}}
                <fieldset>
                    <legend class="text-sm font-semibold text-zinc-700 dark:text-white mb-4 border-b pb-2 w-full">2.
                        Status &
                        Kepegawaian</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Dropdown Status --}}
                        <flux:select name="status" label="Status Pegawai" required>
                            <option value="">Pilih Status...</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonactive" {{ old('status') == 'nonactive' ? 'selected' : '' }}>Pensiun
                            </option>
                        </flux:select>

                        {{-- Dropdown Tipe --}}
                        <flux:select id="tipe_pegawai" name="type" label="Tipe Pegawai" required>
                            <option value="">Pilih Tipe...</option>
                            <option value="ASN" {{ old('type') == 'ASN' ? 'selected' : '' }}>ASN</option>
                            <option value="Non ASN" {{ old('type') == 'Non ASN' ? 'selected' : '' }}>Non ASN</option>
                        </flux:select>

                        <flux:input type="date" name="tmt_start" label="TMT Pangkat Awal (Mulai Tugas)"
                            value="{{ old('tmt_start') }}" required />

                        <flux:input id="tmt_akhir" type="date" name="tmt_end"
                            label="TMT Pangkat Akhir (Kosongkan jika aktif terus)" value="{{ old('tmt_end') }}" />

                        <flux:input id="tmt_kgb" type="date" name="tmt_kgb" label="TMT Kenaikan Gaji Berkala"
                            value="{{ old('tmt_kgb') }}" required />

                        {{-- Foreign Keys --}}
                        {{-- Catatan: Pastikan Anda mengirim $grades, $ranks, dan $positions dari Controller --}}
                        <flux:select name="rank_grade_id" label="Pangkat/Gol" required>
                            <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                            @isset($rank_grades)
                                @foreach($rank_grades as $rank_grade)
                                    <option value="{{ $rank_grade->id }}" {{ old('rank_grade_id') == $rank_grade->id ? 'selected' : '' }}>
                                        @if ($rank_grade->rank_name === null)
                                            {{ $rank_grade->grade_code }}
                                        @else
                                            {{ $rank_grade->rank_name . " - " . "(" . $rank_grade->grade_code . ")" }}
                                        @endif
                                    </option>
                                @endforeach
                            @endisset
                        </flux:select>

                        {{-- <flux:select name="rank_id" label="Pangkat">
                            <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                            @isset($ranks)
                            @foreach($ranks as $rank)
                            <option value="{{ $rank->id }}" {{ old('rank_id')==$rank->id ? 'selected' : '' }}>
                                {{ $rank->rank_name }}
                            </option>
                            @endforeach
                            @endisset
                        </flux:select> --}}

                        <flux:select name="position_id" label="Jabatan" required>
                            <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                            @isset($positions)
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->position_name }}</option>
                                @endforeach
                            @endisset
                        </flux:select>
                    </div>
                </fieldset>

                {{-- Footer / Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <flux:button href="{{ route('pegawai.index') }}" variant="subtle" wire:navigate>
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" class="cursor-pointer bg-emerald-600">
                        Simpan Pegawai
                    </flux:button>
                </div>

            </form>
        </flux:card>

    </div>
    <script>
        document.addEventListener('livewire:navigated', function () {
            console.log('Script loaded'); // Debug: Pastikan script ini dijalankan
            // Ambil elemen berdasarkan ID yang kita buat tadi
            const tipePegawai = document.getElementById('tipe_pegawai');
            const tmtAkhir = document.getElementById('tmt_akhir');

            // Fungsi untuk mengecek dan mengubah status input
            function toggleTmtAkhir() {
                if (tipePegawai.value === 'ASN') {
                    tmtAkhir.disabled = true;
                    tmtAkhir.value = '';
                } else {
                    tmtAkhir.disabled = false;
                }
            }

            // 1. Jalankan saat user mengganti pilihan dropdown
            tipePegawai.addEventListener('change', toggleTmtAkhir);

            // 2. Jalankan satu kali saat halaman pertama kali dimuat 
            // (Penting: agar saat user gagal validasi form dan halaman me-refresh, 
            // status disable tetap mengikuti pilihan old('type') sebelumnya).
            toggleTmtAkhir();
        });
    </script>
</x-layouts::app>