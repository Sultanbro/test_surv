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


class UsersExport implements FromCollection, WithTitle, WithHeadings
{

    public function __construct($title, $headings, $collection)
    {
        $this->collection = $collection;
        $this->headings = $headings;
        //dd(array_unique($users_ids));
        $this->title = $title;
       //dd($group['name']);
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
        return $this->headings;
    }

}