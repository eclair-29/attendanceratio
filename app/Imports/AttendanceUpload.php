<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Ratio;
use App\Models\Attendance;
use Illuminate\Support\Str;
use App\Models\BatchTracker;
use App\Models\Series;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterBatch;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithUpserts;

class AttendanceUpload implements
    ToModel,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    ShouldQueue,
    WithEvents,
    WithCalculatedFormulas,
    WithUpserts
{
    use Importable, RegistersEventListeners;

    public $fileLabel;

    public function __construct(string $fileLabel)
    {
        $this->fileLabel = $fileLabel;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (Str::contains($this->fileLabel, 'PR')) {
            if (!isset($row['id_no']) || Str::contains($row['id_no'], ['PREGNANT', 'Note', 'Working', 'SL/', 'Late'])) {
                return null;
            }
        }

        $attendance = Str::contains($this->fileLabel, 'PR')
            ? new Ratio([
                'series_id' => '2023_12_' . $row['id_no'],
                'series' => $row['series'],
                'staff_code' => $row['id_no'],
                'entity' => $row['entity'],
                'division' => $row['division'],
                'dept' => $row['dept'],
                'section' => $row['section'],
                'shift_type' => $row['shift'],
                'working_days' => $row['working_days'],
                'total_absent' => $row['days_of_absent'],
                'absent_ratio' => $row['absent_ratio'] * 100,
                'attendance_ratio' => $row['ratio'] * 100,
                'sl_percentage' => $row['sl_ratio'] * 100,
                'vl_percentage' => ($row['vl_ratio'] + $row['el_ratio']) * 100,
                'late_percentage' => $row['late_ratio'] * 100,
                'early_exit_percentage' => $row['ut_ratio'] * 100,
                'lwop_percentage' => $row['ua_ratio'] * 100,
                'total_sl' => $row['sl'],
                'total_vl' => $row['vl'] + $row['el'],
                'total_late' => $row['late'],
                'total_early_exit' => $row['ut'],
                'total_lwop' => $row['ua']
            ])
            : new Attendance([
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

        return $attendance;
    }

    public function uniqueBy()
    {
        if (Str::contains($this->fileLabel, 'PR')) {
            return 'series_id';
        }
    }

    public function headingRow(): int
    {
        $headingRow = Str::contains($this->fileLabel, 'PR') ? 2 : 3;
        return $headingRow;
    }

    public function batchSize(): int
    {
        $batch = Str::contains($this->fileLabel, 'PR') ? 100 : 3000;
        return $batch;
    }

    public function chunkSize(): int
    {
        $chunk = Str::contains($this->fileLabel, 'PR') ? 100 : 3000;
        return $chunk;
    }

    public function beforeImport(BeforeImport $event)
    {
        clearQueueTables('attendance');

        $totalRows = array_values($event->getReader()->getTotalRows())[0];
        $batchCount = ceil($totalRows / 3000);

        BatchTracker::create(['current_batch_count' => $batchCount, 'total_batch_count' => $batchCount, 'type' => 'attendance']);
    }

    public function afterBatch(AfterBatch $event)
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

            Ratio::upsert([
                'series_id' => $attendance->series_id,
                'series' => $attendance->series,
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
            ], ['series_id'], [
                'staff_code',
                'series',
                'entity',
                'division',
                'dept',
                'section',
                'working_days',
                'total_absent',
                'absent_ratio',
                'attendance_ratio',
                'sl_percentage',
                'vl_percentage',
                'late_percentage',
                'early_exit_percentage',
                'lwop_percentage',
                'total_sl',
                'total_vl',
                'total_late',
                'total_early_exit',
                'total_lwop'
            ]);
        }

        $currentBatch = BatchTracker::where('type', 'attendance')->first();
        $currentBatch->update([
            'current_batch_count' => $currentBatch->current_batch_count - 1
        ]);
    }

    public function afterImport(AfterImport $event)
    {
        $recentSeriesRatio = Ratio::orderBy('id', 'desc')->first();
        Series::updateOrCreate(['series' => $recentSeriesRatio->series], ['series' => $recentSeriesRatio->series]);
        clearQueueTables('attendance');
        // DB::statement("ALTER TABLE batch_trackers AUTO_INCREMENT = 1");
        // DB::table('vw_consolidated_attendance')->truncate();
    }

    public function importFailed(ImportFailed $event)
    {
        dd($event->getException()->getMessage());
    }
}
