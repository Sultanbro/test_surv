<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCoordinate extends Model
{
    use HasFactory;

    protected $table = 'user_coordinates';

    protected $fillable = [
        'geo_lat',
        'geo_lon',
    ];


    /**
     * Get the user associated with the coordinate.
     * @return hasOne
     */
    public function user():hasOne
    {
        return $this->hasOne(User::class, 'coordinate_id');
    }
}
