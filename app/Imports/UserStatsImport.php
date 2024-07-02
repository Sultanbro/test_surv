<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class UserStatsImport implements ToCollection
{

    public array $data;
    public array $headings;

    public function __construct()
    {

    }

    public function collection(Collection $rows)
    {
      

        $data = [];
        $headings = [];
        if(count($rows) > 0) {
            $headings = $rows[0];
            $this->headings[] = $headings;
        }

        foreach ($rows as $key => $row) 
        {
            if($key == 0) continue;
           
            $item = [];
            foreach ($headings as $key => $heading) {
                $item[$heading] = $row[$key];
            }
            $data[] = $item;
        }

        $this->data[] = $data;
    
        
    }
}