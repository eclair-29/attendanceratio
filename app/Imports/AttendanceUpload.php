<?php

namespace App\Imports;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceUpload implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, ShouldQueue
{
    use Importable;

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
        return 3000;
    }

    public function chunkSize(): int
    {
        return 3000;
    }
}
