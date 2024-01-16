<?php

namespace App\Imports;

use App\Models\StaffBaseDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffBaseDetailsUpload implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable, RegistersEventListeners;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new StaffBaseDetail([
            'staff_code' => $row['employee_code'],
            'status' => $row['employee_status'],
            'shift_type' => $row['shift_type'],
            'dept' => $row['department'],
            'section' => $row['section'],
            'division' => $row['division'],
        ]);
    }

    public function headingRow(): int
    {
        return 4;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
