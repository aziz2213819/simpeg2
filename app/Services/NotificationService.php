<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NotificationService
{
    protected PromotionService $promotionService;

    protected PensiunService $pensiunService;

    public function __construct(PromotionService $promotionService, PensiunService $pensiunService)
    {
        $this->promotionService = $promotionService;
        $this->pensiunService = $pensiunService;
    }

    // Pemetaan jadwal spesifik untuk masing-masing tipe notifikasi
    protected array $typeSchedules = [
        'pangkat' => [
            ['method' => 'subMonths', 'value' => 8, 'label' => '8 Bulan'],
        ],
        'gaji_berkala' => [
            ['method' => 'subMonths', 'value' => 2, 'label' => '2 Bulan'],
        ],
        'pensiun' => [
            ['method' => 'subYears', 'value' => 1, 'label' => '1 Tahun'],
        ],
    ];

    public function checkAndGenerateNotifications($users)
    {
        $now = Carbon::parse(now())->startOfDay();
        // dd($now . "notif");
        // $debugNotif = [];

        // 1. Kumpulkan semua ID Pegawai yang valid
        $employeeIds = [];
        foreach ($users as $user) {
            if ($user->employee) {
                $employeeIds[] = $user->employee->id;
            }
        }

        // 2. QUERY OPTIMIZATION: Tarik SEMUA notifikasi tahun ini hanya dengan 1 Query
        // Gunakan get() dan pilih kolom yang diperlukan saja agar hemat memori RAM
        $existingNotifications = Notification::whereIn('employee_id', $employeeIds)
            ->get(['employee_id', 'type', 'title']);

        // 3. Susun data ke array multidimensi untuk pencarian kilat (O(1) Lookup)
        // Format: $notifCache[employee_id][type] = ['Judul Notif 1', 'Judul Notif 2']
        $notifCache = [];
        foreach ($existingNotifications as $notif) {
            $notifCache[$notif->employee_id][$notif->type][] = $notif->title;
        }

        foreach ($users as $user) {
            $employee = $user->employee;
            if (! $employee) {
                continue;
            }

            foreach ($this->typeSchedules as $type => $schedules) {

                $targetDate = $this->calculateTargetDate($employee, $type);

                if (! $targetDate) {
                    continue;
                }

                if ($targetDate->lte($now)) {
                    continue;
                }

                // if ($type === 'pangkat') {
                // $nextRank = $this->promotionService->getNextRank($employee->rank_grade_id);
                // if (!$nextRank) {
                //     continue;
                // }
                // $nextGol = $this->promotionService->getGolongan($nextRank);
                // if (!$this->promotionService->canPromote($employee, $nextGol)) {
                //     continue;
                // }
                // }

                foreach ($schedules as $schedule) {
                    $triggerDate = $targetDate->copy()->{$schedule['method']}($schedule['value']);
                    if ($now->greaterThanOrEqualTo($triggerDate)) {

                        $alreadyNotified = false;
                        $searchKeyword = $targetDate->format('Y-m-d');

                        // Pastikan key array ada sebelum di-loop untuk menghindari Error Undefined Array Key
                        if (isset($notifCache[$employee->id][$type])) {
                            foreach ($notifCache[$employee->id][$type] as $existingTitle) {
                                if (str_contains($existingTitle, $searchKeyword)) {
                                    $alreadyNotified = true;
                                    break;
                                }
                            }
                        }

                        if (! $alreadyNotified) {
                            // $debugNotif[] = [
                            //     'nama' => $employee->name,
                            //     'jenis' => Str::headline($type),
                            //     'kategori_notif' => 'H-' . $schedule['label'],
                            //     'tanggal_target' => $targetDate->format('d M Y'),
                            //     'tanggal_trigger' => $triggerDate->format('d M Y'),
                            // ];

                            $newTitle = 'Peringatan H-'.$schedule['label'].' '.Str::headline($type).' (pada '.$targetDate->format('Y-m-d').')';

                            $existingSK = Notification::where('employee_id', $employee->id)
                                ->where('type', 'pangkat')
                                ->where('title', 'like', '%'.$targetDate->format('Y-m-d').'%')
                                ->whereIn('status', ['pending', 'rejected'])
                                ->exists();

                            if ($type === 'pangkat' && $existingSK) {
                                continue;
                            }

                            $status = null;
                            // if ($type === 'pangkat' && $needsSK) {
                            //     $status = 'pending';
                            // }

                            Notification::create([
                                'employee_id' => $employee->id,
                                'type' => $type,
                                'title' => $newTitle,
                                'message' => 'Sistem mendeteksi jadwal '.Str::headline($type)." untuk {$employee->name} jatuh pada ".$targetDate->format('d M Y').'. Mohon segera persiapkan berkas yang dibutuhkan.',
                                'status' => null,
                            ]);
                            $notifCache[$employee->id][$type][] = $newTitle;
                        }
                    }
                }
            }
        }
        // dd($debugNotif);
    }

    /**
     * "OTAK" LOGIKA: Menghitung target tanggal berdasarkan aturan masing-masing tipe
     */
    private function calculateTargetDate(Employee $employee, string $type)
    {
        switch ($type) {
            case 'pangkat':
                // Kelipatan 4 Tahun dari TMT
                if (! $employee->tmt_start) {
                    return null;
                }

                return $this->promotionService->getNextTargetDate($employee);

            case 'gaji_berkala':
                // Kelipatan 1 Bulan dari TMT
                if (! $employee->tmt_kgb) {
                    return null;
                }
                $tmt = Carbon::parse($employee->tmt_kgb)->startOfDay();
                $targetDate = $tmt->copy()->addYears(2);

                return $targetDate;

            case 'pensiun':
                // Umur 60 Tahun
                if (! $employee->birth_date) {
                    return null;
                }

                $bday = Carbon::parse($employee->birth_date)->startOfDay();
                $umurPensiun = $this->pensiunService->getPensiunNumber($employee);

                return $bday->copy()->addYears($umurPensiun);

            default:
                return null;
        }
    }
}
