<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $code
 * @property string $name
 * @property string $rate
 * @property string $expired_at
 * @property string $created_by
 */
class PromoCode extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'name',
        'expired_at',
        'rate',
        'created_by',
    ];

    protected $primaryKey = 'code';
}
