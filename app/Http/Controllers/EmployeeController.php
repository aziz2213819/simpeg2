<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Position;
use App\Models\RankGrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rank_grade_id = $request->input('rank_grade_id');
        $education_level = $request->input('education_level');
        $gender = $request->input('gender');
        $rankGrades = RankGrade::orderBy('created_at', 'asc')->get();
        $employees = Employee::query()
            ->with(['rankGrade'])
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                    // ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->when($rank_grade_id, function ($query, $rank_grade_id) {
                return $query->where('rank_grade_id', $rank_grade_id);
            })
            ->when($education_level, function ($query, $education_level) {
                return $query->where('education_level', $education_level);
            })
            ->when($gender, function ($query, $gender) {
                return $query->where('gender', $gender);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('admin.pegawai.index', compact('employees', 'search', 'rank_grade_id', 'rankGrades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rank_grades = RankGrade::all();
        $positions = Position::all();
        return view('admin.pegawai.create', compact('rank_grades', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Definisikan Aturan Validasi
        $rules = [
            // Validasi Akun (Users)
            // 'email'         => 'required|email|unique:users,email',
            // 'password'      => 'required|min:8',

            // Validasi Biodata (Employees)
            // 'nip'           => 'required|string|digits:18|unique:employees,nip',
            'name'          => 'required|string|max:255',
            'birth_date'    => 'required|date',
            'gender'        => 'required|in:l,p',
            'status'        => 'required|string',
            'education_level' => 'required|in:SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3',
            'education_detail' => 'nullable|string|max:255',
            'tmt_start'     => 'required|date',
            'tmt_end'       => 'nullable|date|after_or_equal:tmt_start',
            'tmt_kgb'       => 'required|date|after_or_equal:tmt_start',
            'type'          => 'required|in:ASN,Non ASN',
            'position_id'   => 'required|exists:positions,id',
            'rank_grade_id' => 'nullable|exists:rank_grades,id',
        ];

        // 2. Definisikan Pesan Bahasa Indonesia
        $messages = [
            'required'        => 'Kolom :attribute wajib diisi.',
            'unique'          => ':attribute ini sudah terdaftar di sistem.',
            'digits'          => ':attribute harus berupa angka dan tepat :digits digit.',
            'date'            => 'Format :attribute harus berupa tanggal yang valid.',
            'after_or_equal'  => 'Tanggal :attribute harus sama dengan atau setelah TMT Start.',
            'in'              => 'Pilihan :attribute tidak valid.',
            'exists'          => 'Data :attribute yang dipilih tidak ditemukan.',
        ];

        // 3. Ubah nama atribut agar lebih enak dibaca user (Opsional)
        $attributes = [
            'name'          => 'Nama Lengkap',
            'birth_date'    => 'Tanggal Lahir',
            'gender'        => 'Jenis Kelamin',
            'status'        => 'Status Pegawai',
            'education_level' => 'Pendidikan Terakhir',
            'education_detail' => 'Detail Pendidikan',
            'tmt_start'     => 'TMT Pangkat Awal',
            'tmt_end'       => 'TMT Pangkat Akhir',
            'tmt_kgb'       => 'TMT KGB',
            'type'          => 'Tipe Pegawai',
            'position_id'   => 'Jabatan',
            'rank_grade_id' => 'Pangkat/Gol',
        ];

        // 4. Eksekusi Validasi
        $validatedData = $request->validate($rules, $messages, $attributes);

        // 5. Simpan Data (Gunakan DB Transaction karena ada 2 tabel)
        DB::transaction(function () use ($validatedData) {
            $employee = Employee::create([
                // 'nip'           => $validatedData['nip'],
                'name'          => $validatedData['name'],
                'birth_date'    => $validatedData['birth_date'],
                'gender'        => $validatedData['gender'],
                'status'        => $validatedData['status'],
                'education_level' => $validatedData['education_level'],
                'education_detail' => $validatedData['education_detail'],
                'tmt_start'     => $validatedData['tmt_start'],
                'tmt_end'       => $validatedData['tmt_end'] ?? null,
                'tmt_kgb'       => $validatedData['tmt_kgb'],
                'type'          => $validatedData['type'],
                'position_id'   => $validatedData['position_id'] ?? null,
                'rank_grade_id' => $validatedData['rank_grade_id'] ?? null,
            ]);

            // 1. Pisahkan nama berdasarkan tanda koma, dan ambil bagian pertamanya saja (index 0)
            // "ACHMAD SIDDIK, SAP, MM" -> menjadi "ACHMAD SIDDIK"
            $nameWithoutTitle = explode(',', $validatedData['name'])[0];

            // 2. Bersihkan nama HANYA dari karakter selain huruf (menghapus spasi, titik, kutip, dll)
            // "MOH. YAMIN" -> menjadi "mohyamin"
            // "ACHMAD SIDDIK" -> menjadi "achmadsiddik"
            $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $nameWithoutTitle));

            // 3. Ambil tahun lahir (contoh: 1985)
            $birthYear = date('Y', strtotime($validatedData['birth_date']));

            // 4. Gabungkan agar unik
            $uniqueUsername = $cleanName . $birthYear;

            User::create([
                'name'          => $validatedData['name'],
                'employee_id'   => $employee->id,
                'email'         => "{$uniqueUsername}@gmail.com",
                'password'      => bcrypt('dlhbangkalan564738'), 
            ]);
        });

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai dan Akun berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $pegawai)
    {
        return view('pegawai.show', compact('pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $pegawai)
    {
        $rank_grades = RankGrade::all();
        $positions = Position::all();
        // dd($pegawai);
        return view('admin.pegawai.edit', compact('pegawai', 'rank_grades', 'positions'));
    }
        
        /**
         * Update the specified resource in storage.
        */
    public function update(Request $request, Employee $pegawai)
    {
        $oldTmtKgb = $pegawai->tmt_kgb;
        $oldTmtStart = $pegawai->tmt_start;
        $oldBirthDate = $pegawai->birth_date;
        $rules = [
            // 'email'         => 'required|email|unique:users,email,' . ($user ? $user->id : ''),
            // 'password'      => 'nullable|min:8',
            // 'nip'           => 'required|string|digits:18|unique:employees,nip,' . $pegawai->id,
            'name'          => 'required|string|max:255',
            'birth_date'    => 'required|date',
            'gender'        => 'required|in:l,p',
            'status'        => 'required|string',
            'education_level' => 'required|in:SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3',
            'education_detail' => 'required|string',
            'tmt_start'     => 'required|date',
            'tmt_end'       => 'nullable|date|after_or_equal:tmt_start',
            'tmt_kgb'       => 'required|date|after_or_equal:tmt_start',
            'type'          => 'required|string',
            'position_id'   => 'nullable|exists:positions,id',
            'rank_grade_id' => 'nullable|exists:rank_grades,id',
        ];

        $messages = [
            'required'        => 'Kolom :attribute wajib diisi.',
            'unique'          => ':attribute ini sudah dipakai orang lain.',
            'digits'          => ':attribute harus berupa angka dan tepat :digits digit.',
            'date'            => 'Format :attribute harus berupa tanggal yang valid.',
            'after_or_equal'  => 'Tanggal :attribute harus sama dengan atau setelah TMT Awal.',
            'in'              => 'Pilihan pada kolom :attribute tidak valid.',
            'exists'          => 'Data :attribute yang dipilih tidak ditemukan.',
        ];

        $attributes = [
            // 'nip'           => 'NIP',
            'name'          => 'Nama',
            'birth_date'    => 'Tanggal Lahir',
            'gender'        => 'Jenis Kelamin',
            'tmt_start'     => 'TMT Pangkat Awal',
            'tmt_end'       => 'TMT Pangkat Akhir',
            'tmt_kgb'       => 'TMT Kenaikan Gaji Berkala',
            'type'          => 'Tipe Pegawai',
            'position_id'   => 'Jabatan',
            'rank_grade_id' => 'Pangkat/Gol',
        ];

        $validatedData = $request->validate($rules, $messages, $attributes);

        DB::transaction(function () use ($validatedData, $pegawai) {
            // 1. Update Biodata Pegawai
            $pegawai->update([
                // 'nip'           => $validatedData['nip'],
                'name'          => $validatedData['name'],
                'birth_date'    => $validatedData['birth_date'],
                'gender'        => $validatedData['gender'],
                'status'        => $validatedData['status'],
                'education_level' => $validatedData['education_level'],
                'education_detail' => $validatedData['education_detail'],
                'tmt_start'     => $validatedData['tmt_start'],
                'tmt_end'       => $validatedData['tmt_end'] ?? null,
                'tmt_kgb'       => $validatedData['tmt_kgb'],
                'type'          => $validatedData['type'],
                'position_id'   => $validatedData['position_id'] ?? null,
                'rank_grade_id' => $validatedData['rank_grade_id'] ?? null,
            ]);

            $user = User::where('employee_id', $pegawai->id)->first();
            // 1. Pisahkan nama berdasarkan tanda koma, dan ambil bagian pertamanya saja (index 0)
            // "ACHMAD SIDDIK, SAP, MM" -> menjadi "ACHMAD SIDDIK"
            $nameWithoutTitle = explode(',', $validatedData['name'])[0];

            // 2. Bersihkan nama HANYA dari karakter selain huruf (menghapus spasi, titik, kutip, dll)
            // "MOH. YAMIN" -> menjadi "mohyamin"
            // "ACHMAD SIDDIK" -> menjadi "achmadsiddik"
            $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $nameWithoutTitle));

            // 3. Ambil tahun lahir (contoh: 1985)
            $birthYear = date('Y', strtotime($validatedData['birth_date']));

            // 4. Gabungkan agar unik
            $uniqueUsername = $cleanName . $birthYear;

            $user->update([
                'name' => $validatedData['name'],
                // 'email' => "{$uniqueUsername}@gmail.com",
            ]);
        });

        if ($oldTmtKgb != $request->tmt_kgb) {
            $oldTmtKgb = Carbon::parse($oldTmtKgb)->addYears(2)->format('Y-m-d');
            $query = Notification::where('employee_id', $pegawai->id)
                ->where('type', 'gaji_berkala')
                ->where('title', 'like', '%' . $oldTmtKgb . '%');
            $notif = $query->latest()->first();
            $result = $notif?->delete();
        }

        if ($oldTmtStart != $request->tmt_start) {
            $interval = $pegawai->position->type === 'fungsional'
                ? 3
                : 4;

            $targetDate = Carbon::parse($oldTmtStart)
                ->addYears($interval)
                ->format('Y-m-d');

            $query = Notification::where('employee_id', $pegawai->id)
                ->where('type', 'pangkat')
                ->where('title', 'like', '%' . $targetDate . '%');
            $notif = $query->latest()->first();
            $result = $notif?->delete();
        }

        if ($oldBirthDate != $request->birth_date) {
            $pensiunUmur = (
                $pegawai->position_id == 1 ||
                $pegawai->position_id == 10 ||
                str_contains($pegawai->position->position_name, 'Sekretaris') ||
                str_contains($pegawai->position->position_name, 'Kepala Dinas Lingkungan Hidup')
            ) ? 60 : 58;

            $targetDate = Carbon::parse($oldBirthDate)
                ->addYears($pensiunUmur)
                ->format('Y-m-d');

            $query = Notification::where('employee_id', $pegawai->id)
                ->where('type', 'pensiun')
                ->where('title', 'like', '%' . $targetDate . '%');
            $notif = $query->latest()->first();
            $result = $notif?->delete();
        }

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $pegawai)
    {
        // dd($pegawai);
        $pegawai->delete();

        return redirect()->route('pegawai.index')
                         ->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function export(Request $request) {
        $filters = array_filter($request->only([
            'education_level',
            'gender',
            'rank_grade_id'
        ]));
        return Excel::download(
            new EmployeesExport($filters), 'pegawai.xlsx'
        );
    }

    public function exportPdfKgb()
    {
        $now = now()->startOfDay();

        // rentang target KGB
        $start = $now->copy()->addMonth()->subYears(2)->startOfMonth();
        $end   = $now->copy()->addMonths(2)->subYears(2)->endOfMonth();

        $employees = Employee::with(['rankGrade', 'position'])
            ->whereNotNull('tmt_kgb')
            ->whereBetween('tmt_kgb', [$start, $end])
            ->orderBy('tmt_kgb')
            ->get();

        $periode = strtoupper(
            $start->translatedFormat('F Y')
            . ' - ' .
            $end->translatedFormat('F Y')
        );

        $title = "DAFTAR KENAIKAN GAJI BERKALA (KGB) PERIODE {$periode}";

        $pdf = Pdf::loadView(
            'admin.pdf.pegawai',
            compact('employees', 'periode', 'title')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('pegawai.kgb.pdf');
    }

    public function exportPdfPensiun()
    {
        $now = now()->startOfDay();

        $employees = Employee::with(['rankGrade', 'position'])
            ->whereNotNull('birth_date')
            ->get()
            ->filter(function ($e) use ($now) {

                $positionName = strtolower(
                    optional($e->position)->position_name ?? ''
                );

                // sama persis seperti NotificationService
                $umurPensiun = (
                    $e->position_id == 1 ||
                    $e->position_id == 10 ||
                    str_contains($positionName, 'sekretaris') ||
                    str_contains($positionName, 'kepala dinas')
                ) ? 60 : 58;

                // tanggal pensiun
                $pensiunDate = Carbon::parse($e->birth_date)
                    ->addYears($umurPensiun)
                    ->startOfDay();

                // trigger H-1 tahun
                $triggerDate = $pensiunDate
                    ->copy()
                    ->subYear();

                // SAMA PERSIS DENGAN NOTIF
                return $now->gte($triggerDate);
            })
            ->sortBy(function ($e) {

                $positionName = strtolower(
                    optional($e->position)->position_name ?? ''
                );

                $umurPensiun = (
                    $e->position_id == 1 ||
                    $e->position_id == 10 ||
                    str_contains($positionName, 'sekretaris') ||
                    str_contains($positionName, 'kepala dinas')
                ) ? 60 : 58;

                return Carbon::parse($e->birth_date)
                    ->addYears($umurPensiun);
            })
            ->values();

        $title = "DAFTAR PEGAWAI MEMASUKI MASA PENSIUN";

        $periode = strtoupper(
            'PER ' . $now->translatedFormat('d F Y')
        );

        $pdf = Pdf::loadView(
            'admin.pdf.pegawai',
            compact('employees', 'title', 'periode')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('pegawai.pensiun.pdf');
    }
}
