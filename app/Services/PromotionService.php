<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Notification;
use Carbon\Carbon;

class PromotionService
{
    public function process()
    {
        $employees = Employee::get();
        foreach ($employees as $employee) {

            if (! $this->isEligibleByTime($employee)) {
                continue;
            }

            $nextRank = $this->getNextRank($employee->rank_grade_id);
            if (! $nextRank) {
                continue;
            }

            $nextGol = $this->getGolongan($nextRank);
            if (! $this->canPromote($employee, $nextGol)) {
                continue;
            }

            $targetDate = $this->getTargetDate($employee);
            $latestSK = Notification::where('employee_id', $employee->id)
                ->where('type', 'pangkat')
                ->where('title', 'like', '%'.$targetDate->format('Y-m-d').'%')
                ->latest()
                ->first();

            // tidak ada notif
            if ($latestSK === null) {
                continue;
            }

            if ($latestSK->status === 'rejected') {
                continue;
            }

            if ($latestSK->status === 'pending') {
                continue;
            }

            if (! $latestSK->sk_file_path) {
                continue;
            }

            if ($latestSK->status === 'approved') {
                $this->promote($employee, $nextRank, $targetDate);
            }
        }
    }

    public function isEligibleByTime($employee)
    {
        $targetDate = $this->getTargetDate($employee);
        if (! $targetDate) {
            return false;
        }
        if ($targetDate->equalTo(
            Carbon::parse($employee->tmt_start)->startOfDay()
        )) {
            return false;
        }
        $now = now()->startOfDay();
        // belum masuk periode
        if ($now->lt($targetDate)) {
            return false;
        }
        // grace period 1 bulan
        if ($now->gt($targetDate->copy()->addMonth())) {
            return false;
        }

        return true;
    }

    public function getNextRank($currentId)
    {
        $map = [
            14 => 13, // I/a → I/b
            13 => 12,
            12 => 11,
            11 => 10, // II/a
            10 => 9,
            9 => 8,
            8 => 7,
            7 => 6,   // III/a
            6 => 5,
            5 => 4,
            4 => 3,
            3 => 2,   // IV/a
            2 => 1,   // IV/b
        ];

        return $map[$currentId] ?? null;
    }

    public function canPromote($employee, $nextGol)
    {
        $level = $employee->education_level;
        $rules = [
            1 => ['SD', 'SMP'],
            2 => ['SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'],
            3 => ['D4', 'S1', 'S2', 'S3'],
            4 => ['S2', 'S3'],
        ];

        return in_array($level, $rules[$nextGol] ?? []);
    }

    private function promote($employee, $nextRankId, $targetDate)
    {
        $employee->update([
            'rank_grade_id' => $nextRankId,
            'tmt_start' => $targetDate,
        ]);
    }

    public function getGolongan($rankId)
    {
        return match (true) {
            $rankId >= 11 => 1, // I
            $rankId >= 7 => 2, // II
            $rankId >= 3 => 3, // III
            default => 4, // IV
        };
    }

    public function getTargetDate($employee)
    {
        if (! $employee->tmt_start) {
            return null;
        }
        $interval = $this->getInterval($employee);
        $tmt = Carbon::parse($employee->tmt_start)->startOfDay();
        $now = now()->startOfDay();
        $yearsElapsed = $tmt->diffInYears($now);
        $cycle = floor($yearsElapsed / $interval);

        return $tmt->copy()->addYears($cycle * $interval);
    }

    public function getNextTargetDate($employee)
    {
        if (! $employee->tmt_start) {
            return null;
        }
        $interval = $this->getInterval($employee);
        $tmt = Carbon::parse($employee->tmt_start)->startOfDay();
        $now = now()->startOfDay();
        $yearsElapsed = $tmt->diffInYears($now);
        $cycle = floor($yearsElapsed / $interval);
        $target = $tmt->copy()->addYears($cycle * $interval);
        if ($target->lte($now)) {
            $target->addYears($interval);
        }

        return $target;
    }

    public function getInterval($employee)
    {
        return $employee->position->type === 'fungsional' ? 3 : 4;
    }
}
