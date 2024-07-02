<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class UsersExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{

    /**
     * @var Carbon
     */
    private Carbon $date;

    public function __construct($title, $headings, $collection, $group, $counter, $date)
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

        $this->date = $date;

        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection()
    {

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
        $lastOfMonth = $this->date->lastOfMonth()->format('Y-m-d');

        $countOfTaxes = DB::table('user_tax')->select(DB::raw('COUNT(DISTINCT `user_tax`.`tax_id`) as `count`'))
            ->whereDate('created_at', '<=', $lastOfMonth)
            ->first()->count;
        $indexOfCell = 18 + $countOfTaxes;
        $coordinate = (new Worksheet)->getCellByColumnAndRow($indexOfCell, 0)->getParent()->getCurrentCoordinate();
        $coordinateOfStyle = str_replace('0', '3', $coordinate);

        return [
            AfterSheet::class => function (AfterSheet $event) use ($coordinate, $coordinateOfStyle) {

                $event->sheet->getDelegate()->getStyle("A1:$coordinateOfStyle")
                    ->getFont()
                    ->setBold(true);

                $event->sheet->getDelegate()->getStyle('A3:G3')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('c4dbca');

                $event->sheet->getDelegate()->getStyle('L3:H3')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('3b73c0');

                $event->sheet->getDelegate()->getStyle('O3')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ffc000');

                $event->sheet->getDelegate()->getStyle("P3:$coordinateOfStyle")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('8ccf5b');

                $event->sheet->setCellValue('A' . (7 + $this->counter), 'Уволенные');
                $event->sheet->getDelegate()->getStyle('A' . (7 + $this->counter))
                    ->getFont()
                    ->setBold(true);


                $fields = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];

                /*foreach($this->last as $key => $item){
                     if(is_string($item) || $item > 0)
                         $event->sheet->setCellValue($fields[$key] . (7 + $this->counter), $item);
                }*/

                $last = $coordinate;

                $totals = 'G' . (count($this->collection) + 3) . ':' . $last . (count($this->collection) + 3);
                $totals2 = 'G' . ($this->counter + 5) . ':' . $last . ($this->counter + 5);
                //$event->sheet->prependRow(3, $this->headings);

                $count_fields = ($this->counter + 4);

                $event->sheet->getDelegate()->getStyle('A5:A' . $count_fields)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('H5:H' . $count_fields)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('L5:L' . $count_fields)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('O5:O' . $count_fields)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ffc000');

                $event->sheet->getDelegate()->getStyle('V5:V' . $count_fields)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('W5:W' . $count_fields)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('e0e0e0');

                $event->sheet->getDelegate()->getStyle('H' . (5 + $this->counter) . ":$coordinate" . (5 + $this->counter))
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ddffad');

                $event->sheet->getDelegate()->getStyle($totals)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
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
                    "A3:$coordinateOfStyle",
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                $event->sheet->styleCells(
                    "A5:$coordinate" . $count_fields,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'H' . (5 + $this->counter) . ":$coordinate" . (5 + $this->counter),
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                //polya dlya uvolennyh
                $event->sheet->styleCells(
                    'A' . (9 + $this->counter) . ":$coordinate" . (count($this->collection) + 2),
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'H' . (count($this->collection) + 3) . ":$coordinate" . (count($this->collection) + 3),
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ]
                );

            },
        ];
    }

}