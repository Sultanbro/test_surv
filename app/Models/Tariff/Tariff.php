<?php

namespace App\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    protected $table = 'tariff';

    public static $defaultUserLimit = 5;

    public $timestamps = true;

    protected $casts = [
        'created_at'  => 'date:d.m.Y H:i',
        'updated_at'  => 'date:d.m.Y H:i',
    ];

    protected $fillable = [
        'kind',
        'validity',
        'users_limit',
        'price',
    ];

    /**
     * @return HasMany
     */
    public function tariffPayments(): HasMany
    {
        return $this->hasMany(TariffPayment::class);
    }

    /**
     * Return specific tariff record from DB.
     *
     * @param int $tarriffId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getTarriff(int $tarriffId){
        return $this->where('id', $tarriffId)->get();
    }
}
