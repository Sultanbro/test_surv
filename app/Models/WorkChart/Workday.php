<?php

namespace App\Models\WorkChart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workday extends Model
{
    use HasFactory;

    protected $table = 'workdays';

    protected $fillable = [
        'name'
    ];
}
