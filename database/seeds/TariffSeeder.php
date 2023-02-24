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
    public function run()
    {
        $tarifKinds = TariffKindEnum::getAllValues();
        $validities = TariffValidityEnum::getAllValues();

        $data = array();
        $usersLimit = [5, 50, 100, 1000];
        $price = [0, 0, 7158, 92460, 22966, 220711, 85004, 817227];

        $counter = 0;
        foreach ($tarifKinds as $kindKey => $tarifKind){
            foreach ($validities as $validity){
                $data[] = [
                    'id' => $counter+1,
                    'validity' => $validity,
                    'kind' => $tarifKind,
                    'users_limit' => $usersLimit[$kindKey],
                    'price' => $price[$counter]
                ];
                $counter++;
            }
        }

        DB::table('tariff_payment')->delete();
        DB::table('tariff')->delete();
        DB::table('tariff')->insert($data);
    }
}
