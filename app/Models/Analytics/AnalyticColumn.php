<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AnalyticColumn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'date',
        'order',
    ];


    public static function defaults($group_id, $date) {
        $fields = [
            'name',
            'plan',
            'sum',
            'avg',
        ];

        foreach($fields as $index => $field) {
            self::create([
                'group_id' => $group_id,
                'name'=> $field,
                'date'=> $date,
                'order'=> $index + 1,
            ]);
        }

        $date = Carbon::parse($date);
        
        for($i=1;$i<=$date->daysInMonth;$i++) {
            self::create([
                'group_id' => $group_id,
                'name'=> $i,
                'date'=> $date,
                'order'=> $i + 5,
            ]);
        }

    }
}
