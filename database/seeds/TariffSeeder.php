<?php

namespace Database\Seeders;

use App\Enums\Tariff\TariffKindEnum;
use App\Enums\Tariff\TariffValidityEnum;
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
        $tariffKinds = TariffKindEnum::getAllValues();
        $validates = TariffValidityEnum::getAllValues();

        $data = array();
        $usersLimit = [5, 20, 50, 100];
        $price = [0, 0, 7158, 92460, 22966, 220711, 85004, 817227];

        $counter = 0;
        foreach ($tariffKinds as $kindKey => $tariffKind){
            foreach ($validates as $validity){
                $data[] = [
                    'id' => $counter+1,
                    'validity' => $validity,
                    'kind' => $tariffKind,
                    'users_limit' => $usersLimit[$kindKey],
                    'price' => $price[$counter]
                ];
                $counter++;
            }
        }

        DB::connection('mysql')->table('tariff_payment')->delete();
        DB::connection('mysql')->table('tariff')->delete();
        DB::connection('mysql')->table('tariff')->insert($data);
    }
}
