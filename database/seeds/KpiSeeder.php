<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kpis =  [];
        $faker = Faker::create();
        for ($i = 1; $i <= 10; $i++)
        {
            $kpis[] = [
                'id'              => $i,
                'targetable_id'   => $faker->numberBetween(1, 20),
                'targetable_type' => ['App\User', 'App\ProfileGroup', 'App\Position'],
                'completed_80'    => $faker->numberBetween(0, 100),
                'completed_100'   => $faker->numberBetween(0, 100),
                'lower_limit'     => $faker->numberBetween(0, 100),
                'upper_limit'     => $faker->numberBetween(0, 100),
                'colors'          => json_encode([
                    'red'   => $faker->randomNumber(),
                    'green' => $faker->randomNumber()
                ])
            ];
        }
        foreach ($kpis as $kpi)
        {
            DB::table('kpis')->insert($kpi);
        }
    }
}
