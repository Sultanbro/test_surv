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
     * @param int $tariffId
     * @return string
     */
    public static function calculateExpireDate(
        int $tariffId
    ): string
    {
        $tariff = self::getTariffById($tariffId);
        $date = now()->addMonth();

        if ($tariff->validity == TariffValidityEnum::Annual)
        {
            $date = now()->addYear();
        }

        return $date;
    }

    /**
     * @param int $tariffId
     * @param int $extraUsers
     * @return float
     */
    public static function calculateTotalPrice(
        int $tariffId,
        int $extraUsers
    ): float
    {
        $tariff = self::getTariffById($tariffId);
        $priceForOnePerson = (float)env('PAYMENT_FOR_ONE_PERSON');
        $price = (float)$tariff->price;

        if ($tariff->users_limit > $extraUsers)
        {
            $price = $priceForOnePerson * $extraUsers;
        }

        return $price;
    }
}
