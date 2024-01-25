<?php

namespace App\Imports;

use App\Models\BatchTracker;
use App\Models\StaffBaseDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterBatch;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class StaffBaseDetailsUpload implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, ShouldQueue, WithEvents, WithUpserts
{
    use Importable, RegistersEventListeners;

    public function uniqueBy()
    {
        return 'staff_code';
    }

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
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function beforeImport(BeforeImport $event)
    {
        clearQueueTables('base');

        $totalRows = array_values($event->getReader()->getTotalRows())[0];
        $batchCount = ceil($totalRows / 500);

        BatchTracker::create(['current_batch_count' => $batchCount, 'total_batch_count' => $batchCount, 'type' => 'base']);
    }

    public function afterBatch(AfterBatch $event)
    {
        $currentBatch = BatchTracker::where('type', 'base')->first();
        $currentBatch->update([
            'current_batch_count' => $currentBatch->current_batch_count - 1
        ]);
    }

    public function afterImport(AfterImport $event)
    {
        // clearQueueTables('base');
    }
}
