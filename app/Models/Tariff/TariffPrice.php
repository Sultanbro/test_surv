<?php

namespace App\Models\Tariff;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $tariff_id
 * @property string $value
 * @property string $currency
 */
class TariffPrice extends Model
{

    protected $connection = 'mysql';
    protected $table = 'tariff_prices';
    public $timestamps = false;
    protected $fillable = [
        'value',
        'currency',
        'tariff_id'
    ];

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(
            Tariff::class,
            'tariff_id',
            'id'
        );
    }

    private function scopeWhereCurrency(Builder $query, string $currency): Builder
    {
        return $query->where("currency", $currency);
    }
}
