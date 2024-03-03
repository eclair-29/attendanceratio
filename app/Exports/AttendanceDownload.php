<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceDownload implements FromArray, WithHeadings, WithStrictNullComparison, ShouldAutoSize
{
    use RegistersEventListeners;

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

    public function columnFormats(): array
    {
        $toPercentage = NumberFormat::FORMAT_PERCENTAGE_00;
        return [
            'G' => $toPercentage,
            'H' => $toPercentage,
            'N' => $toPercentage,
            'O' => $toPercentage,
            'P' => $toPercentage,
            'Q' => $toPercentage,
            'R' => $toPercentage,
        ];
    }

    public function headings(): array
    {
        return [
            "EMPLOYEE ID",
            "EMPLOYEE NAME",
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

    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event
            ->sheet
            ->getDelegate();

        $sheet
            ->getStyle('A1:R1')
            ->getFont()
            ->getColor()
            ->setRGB('ffffff');

        $sheet->getStyle('A1:R1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('2962FF');

        $lastCol = $event->sheet->getHighestColumn();
        $lastRow = $event->sheet->getHighestRow();

        $range = 'A1:' . $lastCol . $lastRow;

        $event->sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => StyleBorder::BORDER_THIN,
                    'color' => ['argb' => '#000000'],
                ],
            ],
        ]);
    }
}
