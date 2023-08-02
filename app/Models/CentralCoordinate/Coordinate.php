<?php

namespace App\Models\CentralCoordinate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'coordinates';

    protected $fillable = ['country', 'city', 'geo_lat', 'geo_lon'];
}
