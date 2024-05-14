<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class DecompositionValue extends Model
{
    protected $table = 'decomposition_values';

    public $timestamps = true;

    protected $casts = [
        'values' => 'array',
    ];

    protected $fillable = [
        'name',
        'group_id',
        'date',
        'values',
    ];
    
    /**
     *  $date Y-m-d
     */
    public static function table(int $group_id, string $date) {
        $values = self::where([
            'group_id' => $group_id,
            'date' => $date,
        ])->get();

        $records = [];

        foreach($values as $value) {
            $arr = [
                'id' => $value->id,
                'name' => $value->name,
                'group_id' => $value->group_id,
            ];
            
            $arr = $arr + $value->values;
            array_push($records, $arr);
        }
        
        return [
            'name' => 'error',
            'group_id' => $group_id,
            'records' => $records
        ];
    } 
}
