<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class Proceed extends Model
{
    /**
     * Выручка колл центра
     * Таблица для хранения значений на странице ТОП 
     */
    protected $table = 'proceeds';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'group_id',
        'value',
    ];
    

}
