<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public $timestamps = true;

    protected $table = 'program';

    protected $fillable = [
        'name'
    ];
}
