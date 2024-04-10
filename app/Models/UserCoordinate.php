<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * @property int id
 */
class UserCoordinate extends Model
{
    use HasFactory;

    protected $table = 'user_coordinates';

    protected $fillable = [
        'geo_lat',
        'geo_lon',
    ];


    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'coordinate_id');
    }
}
