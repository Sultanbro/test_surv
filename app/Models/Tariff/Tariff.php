<?php

namespace App\Models\Tariff;

use App\Enums\Tariff\TariffValidityEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * @param int $tariffId
     * @return ?object
     */
    public static function getTariffById(
        int $tariffId
    ): ?object
    {
        return self::query()->find($tariffId);
    }

    /**
     * @return string
     */
    public function calculateExpireDate(): string
    {
        $date = now()->addMonth();

        if ($this->validity == TariffValidityEnum::Annual)
        {
            $date = now()->addYear();
        }

        return $date;
    }

    /**
     * @param int $extraUsers
     * @return float
     */
    public function calculateTotalPrice(
        int $extraUsers
    ): float
    {
        $priceForOnePerson = (float) config('payment')['payment_for_one_person'];
        $price = $this->price;

        if ($extraUsers > 0)
        {
            $price += $priceForOnePerson * $extraUsers;
        }

        return $price;
    }
}
