<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Notification;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $notifications = Notification::with('employee')
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    // Cari berdasarkan Judul Notifikasi
                    $q->where('title', 'like', "%{$search}%")
                      // ATAU cari berdasarkan Nama Pegawai di tabel relasi
                      ->orWhereHas('employee', function ($subQuery) use ($search) {
                          $subQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.notifikasi.index', compact('notifications', 'search', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data pegawai untuk dipilih di form
        $employees = Employee::orderBy('name', 'asc')->get();
        return view('admin.notifikasi.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:pangkat,gaji_berkala,pensiun',
            'message'     => 'required|string',
            'requires_sk' => 'nullable|boolean',
        ];

        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
            'exists'   => 'Data :attribute tidak valid/tidak ditemukan.',
        ];

        $attributes = [
            'employee_id' => 'Pegawai Penerima',
            'type'        => 'Jenis Notifikasi',
            'message'     => 'Isi Pesan',
        ];

        $validated = $request->validate($rules, $messages, $attributes);

        $status = $request->has('requires_sk') ? 'pending' : null;

        $typeLabel = str_replace('_', ' ', $validated['type']);
        $typeLabel = ucwords($typeLabel);
        $now = now();
        $title = "Peringatan {$typeLabel} ({$now->format('Y-m-d')})";

        Notification::create([
            'employee_id' => $validated['employee_id'],
            'type'        => $validated['type'],
            'title'       => $title,
            'message'     => $validated['message'],
            'status'      => $status,
            'is_read'     => false,
        ]);

        return redirect()
            ->route('notifikasi.index')
            ->with('success', 'Notifikasi berhasil dikirim ke pegawai.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notifikasi)
    {
        $employees = Employee::orderBy('name', 'asc')->get();
        return view('admin.notifikasi.edit', compact('notifikasi', 'employees'));
    }

    public function update(Request $request, Notification $notifikasi)
    {
        $rules = [
            // 'employee_id' => 'required|exists:employees,id',
            // 'type'        => 'required|string|max:50',
            // 'title'       => 'required|string|max:255',
            'status'      => 'nullable|in:pending,approved,rejected',
            // 'message'     => 'required|string',
            'is_read'     => 'required|boolean',
        ];

        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
        ];
        $validatedData = $request->validate($rules, $messages);
        $notifikasi->update($validatedData);
        if ($validatedData['status'] === 'approved') {
            // dd("oke");
            $promotionService = app(PromotionService::class);
            $employee = $notifikasi->employee;
            $nextRank = $promotionService->getNextRank($employee->rank_grade_id);
            if ($nextRank) {
                $targetDate = $promotionService->getTargetDate($employee);
                $nextGol = $promotionService->getGolongan($nextRank);
                if ($promotionService->canPromote($employee, $nextGol)) {
                    $employee->update([
                        'rank_grade_id' => $nextRank,
                        'tmt_start'     => $targetDate,
                    ]);
                }
        }
        }
        return redirect()->route('notifikasi.index')->with('success', 'Data notifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notifikasi)
    {
        $notifikasi->delete();

        return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil dihapus.');
    }
}
