<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceDownload implements FromArray
{
    public $ratio;

    public function __construct($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->ratio;
    }
}
