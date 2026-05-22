<x-layouts::app :title="__('Edit Pegawai')">
    <div class="p-6 space-y-6">

        <flux:card>
            {{-- Header Form --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading size="lg" class="text-zinc-700 dark:text-white">Edit Data Pegawai</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-white">Ubah data biodata untuk <b>{{ $pegawai->name }}</b>.
                    </p>
                </div>
                <flux:button href="{{ route('pegawai.index') }}" variant="ghost" icon="arrow-left" wire:navigate>
                    Kembali
                </flux:button>
            </div>

            {{-- Form Start --}}
            {{-- PERHATIKAN: Route diarahkan ke update dan butuh parameter ID --}}
            <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- SECTION 2: Biodata Pribadi --}}
                <fieldset>
                    <legend class="text-sm font-semibold  mb-4 border-b pb-2 w-full dark:text-white">1. Biodata Pribadi
                    </legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- <flux:input name="nip" label="NIP" value="{{ old('nip', $pegawai->nip) }}" required
                            minlength="18" maxlength="18" /> --}}

                        <flux:input name="name" label="Nama Lengkap (Beserta Gelar)"
                            value="{{ old('name', $pegawai->name) }}" required />

                        {{-- Format tanggal harus Y-m-d agar terbaca oleh input type="date" HTML --}}
                        <flux:input type="date" name="birth_date" label="Tanggal Lahir"
                            value="{{ old('birth_date', \Carbon\Carbon::parse($pegawai->birth_date)->format('Y-m-d')) }}"
                            required />

                        <flux:select name="gender" label="Jenis Kelamin" required>
                            <option value="l" {{ old('gender', $pegawai->gender) == 'l' ? 'selected' : '' }}>
                                Laki-Laki</option>
                            <option value="p" {{ old('gender', $pegawai->gender) == 'p' ? 'selected' : '' }}>
                                Perempuan</option>
                        </flux:select>

                        <flux:select name="education_level" label="Jenjang Pendidikan Terakhir" required>
                            <option value="">-- Pilih Jenjang --</option>

                            <option value="SD" {{ old('education_level', $pegawai->education_level) == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('education_level', $pegawai->education_level) == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('education_level', $pegawai->education_level) == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D1" {{ old('education_level', $pegawai->education_level) == 'D1' ? 'selected' : '' }}>D1</option>
                            <option value="D2" {{ old('education_level', $pegawai->education_level) == 'D2' ? 'selected' : '' }}>D2</option>
                            <option value="D3" {{ old('education_level', $pegawai->education_level) == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="D4" {{ old('education_level', $pegawai->education_level) == 'D4' ? 'selected' : '' }}>D4</option>
                            <option value="S1" {{ old('education_level', $pegawai->education_level) == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('education_level', $pegawai->education_level) == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('education_level', $pegawai->education_level) == 'S3' ? 'selected' : '' }}>S3</option>
                        </flux:select>

                        <flux:input type="text" name="education_detail" label="Detail Pendidikan"
                            value="{{ old('education_detail', $pegawai->education_detail) }}"
                            placeholder="Contoh: IPS / Teknik / Manajemen" />
                    </div>
                </fieldset>

                {{-- SECTION 2: Status & Jabatan --}}
                <fieldset>
                    <legend class="text-sm font-semibold text-gray-700 mb-4 border-b pb-2 w-full dark:text-white">2.
                        Status &
                        Kepegawaian</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <flux:select name="status" label="Status Pegawai" required>
                            <option value="active" {{ old('status', $pegawai->status) == 'active' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="nonactive" {{ old('status', $pegawai->status) == 'nonactive' ? 'selected' : '' }}>
                                Pensiun
                            </option>
                        </flux:select>

                        <flux:select id="tipe_pegawai" name="type" label="Tipe Pegawai" required>
                            <option value="ASN" {{ old('type') == 'ASN' ? 'selected' : '' }}>ASN</option>
                            <option value="Non ASN" {{ old('type') == 'Non ASN' ? 'selected' : '' }}>Non ASN</option>
                        </flux:select>

                        <flux:input type="date" name="tmt_start" label="TMT Pangkat Awal (Mulai Tugas)"
                            value="{{ old('tmt_start', \Carbon\Carbon::parse($pegawai->tmt_start)->format('Y-m-d')) }}"
                            required />

                        <flux:input id="tmt_akhir" type="date" name="tmt_end"
                            label="TMT Pangkat Akhir (Kosongkan jika aktif terus)"
                            value="{{ old('tmt_end', $pegawai->tmt_end ? \Carbon\Carbon::parse($pegawai->tmt_end)->format('Y-m-d') : '') }}" />

                        <flux:input id="tmt_kgb" type="date" name="tmt_kgb" label="TMT Kenaikan Gaji Berkala"
                            value="{{ old('tmt_kgb', $pegawai->tmt_kgb ? \Carbon\Carbon::parse($pegawai->tmt_kgb)->format('Y-m-d') : '') }}" />

                        {{-- Foreign Keys --}}
                        <flux:select name="rank_grade_id" label="Pangkat/Gol">
                            <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                            @isset($rank_grades)
                                @foreach($rank_grades as $rank_grade)
                                    <option value="{{ $rank_grade->id }}" {{ old('rank_grade_id', $pegawai->rank_grade_id) == $rank_grade->id ? 'selected' : '' }}>
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
                            <option value="{{ $rank->id }}" {{ old('rank_id', $pegawai->rank_id) == $rank->id ?
                                'selected' : '' }}>{{ $rank->rank_name }}</option>
                            @endforeach
                            @endisset
                        </flux:select> --}}

                        <flux:select name="position_id" label="Jabatan">
                            <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                            @isset($positions)
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id', $pegawai->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->position_name }}
                                    </option>
                                @endforeach
                            @endisset
                        </flux:select>
                    </div>
                </fieldset>

                {{-- Footer / Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <flux:button onclick="window.history.back()" variant="subtle" wire:navigate>
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" class="cursor-pointer bg-emerald-600">
                        Simpan Perubahan
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