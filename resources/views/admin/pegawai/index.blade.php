<x-layouts::app :title="__('Pegawai')">
    <div class="p-6 space-y-6">

        <x-managed-message />

        <flux:card>

            {{-- header --}}
            <div class="flex flex-col gap-4 mb-4">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <flux:heading size="lg">Data Pegawai</flux:heading>
                        <flux:subheading>Kelola data pegawai</flux:subheading>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <form action="{{ route('pegawai.export') }}" method="GET">
                            <input type="hidden" name="rank_grade_id" value="{{ request('rank_grade_id') }}">
                            <input type="hidden" name="education_level" value="{{ request('education_level') }}">
                            <input type="hidden" name="gender" value="{{ request('gender') }}">
                            <flux:button type="submit">
                                Export Excel
                            </flux:button>
                        </form>
                        <form action="{{ route('pegawai.kgb.pdf') }}" method="GET" target="_blank">
                            <flux:button type="submit">
                                Export KGB
                            </flux:button>
                        </form>
                        <form action="{{ route('pegawai.pensiun.pdf') }}" method="GET" target="_blank">
                            <flux:button type="submit">
                                Export Pensiun
                            </flux:button>
                        </form>
                        <flux:button href="{{ route('pegawai.create') }}" wire:navigate>
                            Tambah Pegawai
                            </flux:button> </div>
                    </div>

                    <form action="{{ route('pegawai.index') }}" method="GET" class="flex flex-col lg:flex-row gap-2">
                        <div class="w-full lg:w-56">
                            <flux:select name="rank_grade_id" onchange="this.form.submit()">
                                <option value="">Semua Golongan</option>
                                @foreach($rankGrades as $rankGrade)
                                    <option value="{{ $rankGrade->id }}" {{ request('rank_grade_id') == $rankGrade->id ? 'selected' : '' }}>
                                                {{ $rankGrade->grade_code }}
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-full lg:w-48">
                            <flux:select name="education_level" onchange="this.form.submit()">
                                <option value="">Semua Pendidikan</option>
                                @foreach(['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $edu)
                                    <option value="{{ $edu }}" {{ request('education_level') == $edu ? 'selected' : '' }}>
                                        {{ $edu }}
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-full lg:w-40">
                            <flux:select name="gender" onchange="this.form.submit()">
                                <option value="">Semua Gender</option>
                                <option value="l" {{ request('gender') == 'l' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="p" {{ request('gender') == 'p' ? 'selected' : '' }}>Perempuan</option>
                            </flux:select>
                        </div>
                        <div class="flex w-full lg:flex-1 gap-2">
                            <flux:input name="search" type="search" value="{{ request('search') }}"
                                placeholder="Cari Nama atau NIP..." class="w-full" />
                            <flux:button type="submit">Cari</flux:button>
                        </div>

                    </form>

                </div>

                {{-- Table --}}
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Nama</flux:table.column>
                        <flux:table.column>Tanggal Lahir</flux:table.column>
                        <flux:table.column>Tipe</flux:table.column>
                        <flux:table.column>Jenis Kelamin</flux:table.column>
                        <flux:table.column>TMT Pangkat</flux:table.column>
                            <flux:table.column class="text-right">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows> @forelse($employees as $employee) <flux:table.row
                                    wire:key="row-{{ $employee->id }}">
                                    <flux:table.cell>
                                        {{ $employee->name }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('d-m-Y') : '-' }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        {{ $employee->type }}
                                        </flux:table.cell>

                                        <flux:table.cell>
                                            {{ $employee->gender == 'l' ? 'L' : 'P' }}
                                            </flux:table.cell>

                                            <flux:table.cell>
                                                @if($employee->type === 'Non ASN')
                                                    {{ \Carbon\Carbon::parse($employee->tmt_start)->format('d-m-Y') }} <br>
                                                    s/d <br>
                                                    {{ $employee->tmt_end ? \Carbon\Carbon::parse($employee->tmt_end)->format('d-m-Y') : 'Sekarang' }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($employee->tmt_start)->format('d-m-Y') }}
                                                @endif
                                            </flux:table.cell>

                                            <flux:table.cell class="flex justify-start gap-2">
                                                <flux:button size="sm" href="{{ route('pegawai.edit', $employee->id) }}"
                                                    wire:navigate>
                                                    Edit
                                                </flux:button>

                                                <flux:modal.trigger name="delete-employee-{{ $employee->id }}">
                                                    <flux:button size="sm" variant="danger" class="cursor-pointer">
                                                        Hapus
                                                        </flux:button>
                                                </flux:modal.trigger>
                                                <flux:modal name="delete-employee-{{ $employee->id }}" class="min-w-88">
                                                    {{-- Form diarahkan ke fungsi destroy di Controller --}}
                                                    <form action="{{ route('pegawai.destroy', $employee->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <div class=" space-y-6">
                                                        <div>
                                                            <flux:heading size="lg">Hapus Data Pegawai?</flux:heading>
                                                            <flux:subheading>
                                                                Apakah Anda yakin ingin menghapus <b>{{ $employee->name }}</b>?
                                                                Tindakan
                                                                ini tidak dapat dibatalkan dan akan menghapus semua data yang
                                                                terkait.
                                                            </flux:subheading>
                                                        </div>

                                                        <div class="flex justify-end gap-2">
                                                            <flux:modal.close>
                                                                <flux:button variant="ghost">Batal</flux:button>
                                                            </flux:modal.close>

                                                            <flux:button type="submit" variant="danger" class="cursor-pointer">
                                                                Ya, Hapus Data
                                                            </flux:button>
                                                        </div>
                        </div>
                        </form>

                        </flux:modal>
                        </flux:table.cell>
                            </flux:table.row>
                    @empty
                {{-- Tampilan jika data tidak ditemukan saat pencarian --}}
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center py-4 text-gray-500">
                                        Tidak ada data pegawai yang ditemukan.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
            </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $employees->links() }}
            </div>

        </flux:card>
    </div>
</x-layouts::app>