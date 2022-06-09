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
        //dd($collection);
        //$this->last = collect(end($collection));
        //dd($this->last);
        $this->counter = $counter;
        //array_pop($collection);
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
  
                 $event->sheet->getDelegate()->getStyle('A1:U3')
                                ->getFont()
                                ->setBold(true);                

                $event->sheet->getDelegate()->getStyle('A3:G3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('c4dbca');

                $event->sheet->getDelegate()->getStyle('H3:L3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('3b73c0');

                $event->sheet->getDelegate()->getStyle('M3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ffc000');

                $event->sheet->getDelegate()->getStyle('N3:U3')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('8ccf5b');

               $event->sheet->setCellValue('A' . (7 + $this->counter), 'Уволенные');
               $event->sheet->getDelegate()->getStyle('A' . (7 + $this->counter))
                                ->getFont()
                                ->setBold(true);  
               

               $fields = ['A', 'B', 'C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R', 'S', 'T', 'U'];

               /*foreach($this->last as $key => $item){
                    if(is_string($item) || $item > 0)
                        $event->sheet->setCellValue($fields[$key] . (7 + $this->counter), $item);
               }*/

                $last = 'U';

               $totals = 'F' . (count($this->collection) + 3) . ':' . $last  . (count($this->collection) + 3);
               $totals2 = 'F' . ($this->counter + 5) . ':' . $last  . ($this->counter + 5);
                //$event->sheet->prependRow(3, $this->headings);
  
                $count_fields = ($this->counter + 4);

                $event->sheet->getDelegate()->getStyle('A5:A'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('F5:F'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('J5:J'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('M5:M'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ffc000');

                $event->sheet->getDelegate()->getStyle('T5:T'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('U5:U'. $count_fields)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('F'.(5 + $this->counter).':U'.(5 + $this->counter))
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ddffad');

                $event->sheet->getDelegate()->getStyle($totals)
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ddffad');

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
                    'A5:U'. $count_fields,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'F'.(5 + $this->counter).':U'.(5 + $this->counter),
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                //polya dlya uvolennyh
                $event->sheet->styleCells(
                    'A'.(9 + $this->counter).':U'.(count($this->collection) + 2),
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'F'.(count($this->collection) + 3).':U'.(count($this->collection) + 3),
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