<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceDownload implements FromArray, WithHeadings, WithStrictNullComparison, ShouldAutoSize
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

    public function headings(): array
    {
        return [
            "EMPLOYEE ID",
            "DIVISION",
            "DEPARTMENT",
            "SECTION",
            "ENTITY",
            "RATIO",
            "ABSENT RATIO",
            "SL",
            "VL",
            "UA",
            "LATE",
            "UT",
            "SL%",
            "VL%",
            "UA%",
            "LATE%",
            "UT%",
        ];
    }
}
