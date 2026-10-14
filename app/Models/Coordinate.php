<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
    protected $table = 'coordinates';

    use HasFactory;

    protected $fillable = [
        'address',
        'country',
        'city',
        'geo_lat',
        'geo_lon',
        'type'
    ];
}
