<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Calendar;
use App\Models\Ratio;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceUpload implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable, RegistersEventListeners;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Attendance([
            'staff_code' => $row['employee_code'],
            'date' => Carbon::parse(Date::excelToDateTimeObject($row['date']))->toDateString(),
            'entity' => $row['entity'],
            'shift' => $row['shift'],
            'shift_st' => $row['shift_st'],
            'att_st' => $row['att_st'],
            'shift_end' => $row['shift_end'],
            'att_end' => $row['att_end'],
            'late' => $row['late'],
            'early_exit' => $row['early_exit'],
            'holiday' => $row['holiday'],
            'leave_type' => $row['leave_type'],
            'np' => $row['np'],
            'other_leaves' => $row['other_leaves'],
            'tardy' => $row['tardy'],
            'ut' => $row['ut'],
            'lwop' => $row['lwop'],
            'adjust' => $row['adjustment'],
        ]);
    }

    public function headingRow(): int
    {
        return 3;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function afterImport(AfterImport $event)
    {

        $consolidatedAttendances = DB::table('vw_consolidated_attendance')->get();

        foreach ($consolidatedAttendances as $attendance) {
            $totalSl = $attendance->sl
                + $attendance->sl_half
                + $attendance->sbl
                + $attendance->sbl_half
                + $attendance->sblw
                + $attendance->sblw_half;

            $totalVl = $attendance->vl
                + $attendance->vl_half
                + $attendance->aa
                + $attendance->aa_half
                + $attendance->el
                + $attendance->el_half
                + $attendance->pl
                + $attendance->pl_half
                + $attendance->spl
                + $attendance->spl_half
                + $attendance->vawc
                + $attendance->vawc_half
                + $attendance->wl
                + $attendance->wl_half
                + $attendance->offset
                + $attendance->offset_half;

            $totalLate = $attendance->late;
            $totalEarlyExit = $attendance->early_exit;
            $totalLwop = $attendance->lwop + $attendance->lwop_half;

            $absentCount = $totalSl
                + $totalVl
                + $totalLate
                + $totalEarlyExit
                + $totalLwop;

            $absentRatio = ($absentCount / $attendance->working_days) * 100;
            $attendanceRatio = 100 - $absentRatio;
            $slPercentage = ($totalSl / $attendance->working_days) * 100;
            $vlPercentage = ($totalVl / $attendance->working_days) * 100;
            $latePercentage = ($totalLate / $attendance->working_days) * 100;
            $earlyExitPercentage = ($totalEarlyExit / $attendance->working_days) * 100;
            $lwopPercentage = ($totalLwop / $attendance->working_days) * 100;

            Ratio::create([
                'staff_code' => $attendance->staff_code,
                'entity' => $attendance->entity,
                'division' => $attendance->division,
                'dept' => $attendance->dept,
                'section' => $attendance->section,
                'shift_type' => $attendance->shift_type,
                'working_days' => $attendance->working_days,
                'total_absent' => $absentCount,
                'absent_ratio' => $absentRatio,
                'attendance_ratio' => $attendanceRatio,
                'sl_percentage' => $slPercentage,
                'vl_percentage' => $vlPercentage,
                'late_percentage' => $latePercentage,
                'early_exit_percentage' => $earlyExitPercentage,
                'lwop_percentage' => $lwopPercentage,
                'total_sl' => $totalSl,
                'total_vl' => $totalVl,
                'total_late' => $totalLate,
                'total_early_exit' => $totalEarlyExit,
                'total_lwop' => $totalLwop
            ]);

            Attendance::truncate();
            // DB::table('vw_consolidated_attendance')->truncate();
        }
    }
}
