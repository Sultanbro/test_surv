<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\FromCollection;


class AnalyticsExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{

    public function __construct($title, $headings, $collection, $group)
    {
        $this->collection = $collection;
        $this->headings = $headings;
        $this->name = $group['name'];
        $this->title = $title;
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection(){

        return collect($this->collection);
    }

    public function headings(): array
    {
        return [
            [$this->name],
            [],
            $this->headings
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
  
                 $event->sheet->getDelegate()->getStyle('A1:AL3')
                                ->getFont()
                                ->setBold(true);                

                $event->sheet->getDelegate()->getStyle('A3:AK3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('c4dbca');
            },
        ];
    }

}