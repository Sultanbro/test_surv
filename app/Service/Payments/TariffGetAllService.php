<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\Models\Tariff\Tariff;
use App\Traits\CurrencyTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;

final class TariffGetAllService
{
    use CurrencyTrait;

    /**
     * @param int $ownerId
     * @return Collection<int, Tariff>
     * @throws Exception
     */
    public function handle(): Collection
    {
        $tariffs = Tariff::all();

        foreach ($tariffs as $tariff) {
            $tariff->multiCurrencyPrice = $this->createMultiCurrencyPrice($tariff->price);
        }

        return $tariffs;
    }
}