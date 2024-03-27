<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\Models\Tariff\Tariff;
use App\Traits\CurrencyTrait;
use Exception;

final class TariffGetAllService
{
    use CurrencyTrait;

    private float $priceForOnePersonInKzt;

    public function __construct()
    {
        $this->priceForOnePersonInKzt = (float) config('payment.payment_for_one_person');
    }

    /**
     * @return array
     * @throws Exception
     */
    public function handle(): array
    {
        $tariffs = Tariff::all();

        foreach ($tariffs as $tariff) {
            $tariff->multiCurrencyPrice = $this::createMultiCurrencyPrice((float) $tariff->price);
        }

        return array(
            'tariffs' => $tariffs,
            'priceForOnePerson' => $this::createMultiCurrencyPrice($this->priceForOnePersonInKzt),
        );
    }
}