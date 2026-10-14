<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\AnalyticsExport;

class AnalyticsImport implements WithMultipleSheets 
{

    public function __construct($data, $group)
    {

        //dd($data['counter']);
        $this->data = $data;
        $this->group = $group;
    }

    public function sheets(): array
    {
        //dd($matches);
        $collections = [];
        foreach($this->data as $list) {
            //dd($list['counter']);
            $collections[] = new AnalyticsExport($list['title'], $list['headings'],$list['sheet'],$this->group);
        }
        return $collections;
    }
}