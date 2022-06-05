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


class UsersExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{

    public function __construct($title, $headings, $collection, $group, $counter)
    {
        //dd();
        $this->last = collect(end($collection));
        //dd($this->last);
        $this->counter = $counter;
        array_pop($collection);
        $this->collection = $collection;
        $this->headings = $headings;
        $this->name = $group['name'];
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
  
                

                $event->sheet->getDelegate()->getStyle('A3:G3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('00FF00');

                $event->sheet->getDelegate()->getStyle('H3:L3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('326ba8');

                $event->sheet->getDelegate()->getStyle('M3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('c7a43a');

                $event->sheet->getDelegate()->getStyle('N3:U3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('00FF00');

               $event->sheet->setCellValue('A' . (6 + $this->counter), 'Уволенные');
               

               $fields = ['A', 'B', 'C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R', 'S', 'T', 'U'];

               foreach($this->last as $key => $item){
                    $event->sheet->setCellValue($fields[$key] . (7 + $this->counter), $item);
               }

                $last = 'U';

               $totals = 'F' . (count($this->collection) + 4) . ':' . $last  . (count($this->collection) + 4);
               $totals2 = 'F' . ($this->counter + 5) . ':' . $last  . ($this->counter + 5);
                //$event->sheet->prependRow(3, $this->headings);
  
                $count_fields = ($this->counter + 3);

                $event->sheet->getDelegate()->getStyle('A4:A'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('F4:F'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('J4:J'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('M4:M'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('c7a43a');

                $event->sheet->getDelegate()->getStyle('T4:T'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('U4:U'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                /*
                    // Итоговые колонки

                    $sheet->cells('F5:F'. $count_fields, function($cell) {
                        $cell->setBackground('#e0e0e0'); 
                    });

                    $sheet->cells('J5:J'. $count_fields, function($cell) {
                        $cell->setBackground('#e0e0e0'); 
                    });
    
                    $sheet->cells('M5:M'. $count_fields, function($cell) {
                        $cell->setBackground('#ffc000'); 
                    });
    
                    $sheet->cells('T5:T'. $count_fields, function($cell) {
                        $cell->setBackground('#e0e0e0'); 
                        $cell->setAlignment('right');
                    });

                    $sheet->cells('U5:U'. $count_fields, function($cell) {
                        $cell->setBackground('#eeeeee'); 
                        $cell->setAlignment('right');
                    });
                */

                $event->sheet->styleCells(
                    'A3:U3',
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'A4:A'. $count_fields,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

            },
        ];
    }

}