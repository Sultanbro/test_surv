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

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'name',
        'expired_at',
        'rate',
        'created_by',
    ];

    protected $primaryKey = 'code';
    public $incrementing = false;

    public static function first(string $code): PromoCode
    {
        /** @var PromoCode */
        return self::query()->where('code', $code)->first();
    }
}
