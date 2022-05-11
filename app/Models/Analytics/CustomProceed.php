<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomProceed extends Model
{
    /**
     * Таблица для хранения выручки специальное редактируемое поле на странице ТОП 
     */
    protected $table = 'custom_proceeds';

    public $timestamps = true;

    protected $fillable = [
        'date', // month
        'value',
        'order', // по счету какая очередь создана
        'name', // название ряда
    ];
    
}

