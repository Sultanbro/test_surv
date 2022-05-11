<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;

class TopEditedValue extends Model
{
    /**
     * Таблица для хранения значений на странице ТОП
     */
    protected $table = 'top_edited_values';

    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'date', // month
        'value'
    ];
    
}

