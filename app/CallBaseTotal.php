<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallBaseTotal extends Model
{
    protected $dates = ['date'];

    protected $fillable = [
        'date',
        'group_id',
        'name',
        'value',
    ];
}
