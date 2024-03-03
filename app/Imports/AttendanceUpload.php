<?php

namespace App\Imports;

use Throwable;
use Carbon\Carbon;
use App\Models\Ratio;
use App\Models\Series;
use App\Models\Attendance;
use Illuminate\Support\Str;
use App\Models\BatchTracker;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterBatch;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class AttendanceUpload implements
    ToModel,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    ShouldQueue,
    WithEvents,
    WithCalculatedFormulas,
    WithUpserts,
    WithValidation,
    SkipsEmptyRows
{
    use Importable, RegistersEventListeners;

    public $fileLabel;
    public $fileDetails;

    public function __construct(string $fileLabel, $fileDetails)
    {
        $this->fileLabel = $fileLabel;
        $this->fileDetails = $fileDetails;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (Str::contains($this->fileLabel, config('constants.agency_prefix'))) {
            if (Str::contains($row['IS ON MATERNITY LEAVE'], ['Yes', 'yes', 'YES'])) {
                return null;
            }
            if (!isset($row['ID NO.']) || Str::contains($row['ID NO.'], ['PREGNANT', 'Note', 'Working', 'SL/', 'Late'])) {
                return null;
            }
        }

        if (Str::contains($this->fileLabel, 'PA')) {
            if (Str::contains($row['LEAVE TYPE'], ['Maternity'])) {
                return null;
            }
        }

        $attendance = Str::contains($this->fileLabel, config('constants.agency_prefix'))
            ? new Ratio([
                'series_id' => $row['SERIES'] . '_' . $row['ID NO.'],
                'series' => $row['SERIES'],
                'staff_code' => $row['ID NO.'],
                'staff' => $row['NAME'],
                'entity' => $row['ENTITY'],
                'division' => $row['DIVISION'],
                'dept' => $row['DEPARTMENT'],
                'section' => $row['SECTION'],
                'shift_type' => $row['SHIFT'],
                'working_days' => $row[cell('WORKING DAYS')],
                'total_absent' => $row['Days of Absent'],
                'absent_ratio' => $row[cell('ABSENT RATIO')] * 100,
                'attendance_ratio' => $row[cell('RATIO')] * 100,
                'sl_percentage' => $row['SL %'] * 100,
                'vl_percentage' => ($row['VL %'] + $row['EL %']) * 100,
                'late_percentage' => $row['LATE %'] * 100,
                'early_exit_percentage' => $row['UT %'] * 100,
                'lwop_percentage' => $row['UA %'] * 100,
                'total_sl' => $row['SL'],
                'total_vl' => $row['VL'] + $row['EL'],
                'total_late' => $row['LATE'],
                'total_early_exit' => $row['UT'],
                'total_lwop' => $row['UA']
            ])
            : new Attendance([
                'staff_code' => $row['EMPLOYEE CODE'],
                'date' => Carbon::parse(Date::excelToDateTimeObject($row['DATE']))->toDateString(),
                'entity' => $row['ENTITY'],
                'shift' => $row['SHIFT'],
                'shift_st' => $row['SHIFT ST'],
                'att_st' => $row['ATT. ST'],
                'shift_end' => $row['SHIFT END'],
                'att_end' => $row['ATT. END'],
                'late' => $row['LATE'],
                'early_exit' => $row['EARLY EXIT'],
                'holiday' => $row['HOLIDAY'],
                'leave_type' => $row['LEAVE TYPE'],
                'np' => $row['NP'],
                'other_leaves' => $row['OTHER LEAVES'],
                'tardy' => $row['TARDY'],
                'ut' => $row['UT'],
                'lwop' => $row['LWOP'],
                'adjust' => $row['ADJUSTMENT'],
            ]);

        return $attendance;
    }

    public function isEmptyWhen(array $row): bool
    {
        if (Str::contains($this->fileLabel, config('constants.agency_prefix'))) {
            return Str::contains($row['IS ON MATERNITY LEAVE'], ['Yes', 'yes', 'YES']) ||
                !isset($row['ID NO.']) || !Str::of($row['ID NO.'])->test('/^\d{7}$/');
        }

        if (Str::contains($this->fileLabel, 'PA')) {
            return Str::contains($row['LEAVE TYPE'], ['Maternity']);
        }
    }

    public function rules(): array
    {
        $rules = Str::contains($this->fileLabel, config('constants.agency_prefix'))
            ? [
                'UT %' => [
                    'required',
                    'numeric',
                    'between:0,100'
                ],
            ]
            : [];

        return $rules;
    }

    public function uniqueBy()
    {
        if (Str::contains($this->fileLabel, config('constants.agency_prefix'))) {
            return 'series_id';
        }
    }

    public function headingRow(): int
    {
        $headingRow = Str::contains($this->fileLabel, config('constants.agency_prefix')) ? 2 : 3;
        return $headingRow;
    }

    public function batchSize(): int
    {
        $batch = Str::contains($this->fileLabel, config('constants.agency_prefix')) ? 100 : 3000;
        return $batch;
    }

    public function chunkSize(): int
    {
        $chunk = Str::contains($this->fileLabel, config('constants.agency_prefix')) ? 100 : 3000;
        return $chunk;
    }

    public function beforeImport(BeforeImport $event)
    {
        clearQueueTables('attendance');
        $batchSize = Str::contains($this->fileLabel, config('constants.agency_prefix')) ? 100 : 3000;

        $totalRows = array_values($event->getReader()->getTotalRows())[0];
        $batchCount = ceil($totalRows / $batchSize);

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

            // if ($attendanceRatio > 100) $attendanceRatio = 100;
            // if ($absentRatio < 0)  $absentRatio = 0;

            Ratio::upsert([
                'series_id' => $attendance->series_id,
                'series' => $attendance->series,
                'staff_code' => $attendance->staff_code,
                'staff' => $attendance->staff,
                'entity' => $attendance->entity,
                'division' => $attendance->division,
                'dept' => $attendance->dept,
                'section' => $attendance->section,
                'shift_type' => $attendance->shift_type,
                // 'leave_type' => $attendance->leave_type,
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
                'staff',
                'series',
                'entity',
                'division',
                'dept',
                'section',
                // 'leave_type',
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
        $recentSeriesRatio = Ratio::whereNotNull('series')->orderBy('id', 'desc')->first();
        Series::updateOrCreate(['series' => $recentSeriesRatio->series], ['series' => $recentSeriesRatio->series]);

        saveUploadedFile(
            $this->fileDetails['fileLabel'],
            $this->fileDetails['fileSize'],
            $this->fileDetails['filePath'],
            $this->fileDetails['fileType']
        );
        // clearQueueTables('attendance');
        // DB::statement("ALTER TABLE batch_trackers AUTO_INCREMENT = 1");
        // DB::table('vw_consolidated_attendance')->truncate();
    }

    public function failed(Throwable $th)
    {
        // dd($th->getMessage());
        // clearQueueTables('base');
        $batchTrack = BatchTracker::where('type', 'attendance')->first();

        $batchTrack->update([
            'error' => $th->getMessage()
        ]);
    }
}
