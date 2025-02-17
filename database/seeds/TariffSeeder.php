<?php

namespace Database\Seeders;

use App\Enums\Tariff\TariffKindEnum;
use App\Enums\Tariff\TariffValidityEnum;
use App\Models\Tariff\Tariff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('mysql')->table('tariff_prices')->truncate();
        DB::connection('mysql')->table('tariff_subscriptions')->truncate();
        DB::connection('mysql')->table('tariff')->truncate();
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');
        $tariffs = config('tariffs');
        $validates = [
            'monthly' => 1,
            'threeMonthly' => 3,
            'yearly' => 12,
        ];

        foreach ($tariffs as $name => $tariff) {
            foreach ($validates as $validity => $monthsCount) {
                $tariffModel = new Tariff();
                $tariffModel->kind = $name;
                $tariffModel->validity = $validity;
                $tariffModel->users_limit = $tariff['users_limit'];
                $tariffModel->save();
                $salePrice = 0;
                foreach ($tariff['prices'] as $currency => $price) {

                    if ($monthsCount > 1) {
                        $salePrice = ($price * $monthsCount) * $tariff['sale_percent'] / 100;
                    }

                    $tariffModel->prices()->create([
                        'currency' => $currency,
                        'value' => ($price * $monthsCount) - $salePrice,
                    ]);
                }
            }
        }
    }
}
