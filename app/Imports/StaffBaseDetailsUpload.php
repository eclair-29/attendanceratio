<?php

namespace App\Imports;

use Illuminate\Support\Str;
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
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\ImportFailed;
use Throwable;

class StaffBaseDetailsUpload implements
    ToModel,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    ShouldQueue,
    WithEvents,
    WithUpserts,
    WithValidation,
    SkipsEmptyRows
{
    use Importable, RegistersEventListeners;

    public $fileDetails;

    public function __construct($fileDetails)
    {
        $this->fileDetails = $fileDetails;
    }

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
        $division = $row[cell('DIVISION')];

        /*  if (!isset($division)) {
            return null;
        } */

        if (!isset($row)) {
            return null;
        }

        return new StaffBaseDetail([
            'staff_code' => $row[cell('EMPLOYEE CODE')],
            'staff' => $row[cell('FIRST NAME')] . ' ' . ($row[cell('MIDDLE NAME')] ?? '') . ' ' . $row[cell('LAST NAME')],
            // 'email' => $row['Official Email'],
            'status' => $row[cell('EMPLOYEE STATUS')],
            'shift_type' => $row[cell('SHIFT TYPE')],
            'dept' => $row[cell('DEPARTMENT')],
            'section' => $row[cell('SECTION')],
            'division' => $row[cell('DIVISION')],
        ]);
    }

    public function rules(): array
    {
        return [
            cell('DIVISION') => 'required',
            cell('DEPARTMENT') => 'required',
            cell('SECTION') => 'required',
            cell('EMPLOYEE STATUS') => 'required',
            cell('SHIFT TYPE') => 'required',
            cell('SHIFT TYPE') => 'required',
            cell('LAST NAME') => 'required',
            cell('EMPLOYEE CODE') => 'required',
        ];
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
        saveUploadedFile(
            $this->fileDetails['fileLabel'],
            $this->fileDetails['fileSize'],
            $this->fileDetails['filePath'],
            $this->fileDetails['fileType']
        );
    }

    public function failed(Throwable $th)
    {
        // dd($th->getMessage());
        // clearQueueTables('base');
        $batchTrack = BatchTracker::where('type', 'base')->first();

        $batchTrack->update([
            'error' => $th->getMessage()
        ]);
    }
}
