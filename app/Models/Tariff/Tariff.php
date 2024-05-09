<?php

namespace App\Models\Tariff;

use App\Enums\Tariff\TariffValidityEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price;

/**
 * @property  string $kind
 * @property  string $validity
 * @property  int $users_limit
 * @property Collection<TariffPrice> $prices
 */
class Tariff extends Model
{
    protected $connection = 'mysql';

    protected $table = 'tariff';

    public static int $defaultUserLimit = 5;

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'date:d.m.Y H:i',
        'updated_at' => 'date:d.m.Y H:i',
    ];

    protected $fillable = [
        'kind',
        'validity',
        'users_limit',
    ];

    /**
     * @param int $tariffId
     * @return ?Tariff
     */
    public static function find(
        int $tariffId
    ): ?Tariff
    {
        /** @var Tariff */
        return self::query()->find($tariffId);
    }

    /**
     * @return string
     */
    public function calculateExpireDate(): string
    {
        $date = now()->addMonth();

        if ($this->validity == TariffValidityEnum::Annual) {
            $date = now()->addYear();
        }

        return $date;
    }

    public function prices(): HasMany
    {
        return $this->hasMany(
            TariffPrice::class,
            'tariff_id',
            'id'
        );
    }
}
