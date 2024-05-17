<?php
declare(strict_types=1);

namespace App\Service\Payment\Core;

use App\Models\Tariff\Tariff;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TariffListService
{
    private array $priceForOnePersonWithCurrencies;

    public function __construct()
    {
        $this->priceForOnePersonWithCurrencies = [
            'kzt' => config('payment.payment_for_one_person_kzt'),
            'rub' => config('payment.payment_for_one_person_rub')
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function handle(): array
    {
        /** @var Collection<Tariff> $tariffs */
        $tariffs = Tariff::with('prices')->get();

        foreach ($tariffs as $tariff) {
            $tariff->multiCurrencyPrice = $tariff->prices->pluck('value', 'currency')->toArray();
        }

        return array(
            'tariffs' => $tariffs,
            'priceForOnePerson' => $this->priceForOnePersonWithCurrencies,
        );
    }
}