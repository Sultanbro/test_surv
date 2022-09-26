<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KpiItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $items = [];

        for ($i = 1; $i <= 10; $i++)
        {
            $items[] = [
                [
                    'id'            => $i,
                    'name'          => $faker->name,
                    'activity_id'   => $faker->numberBetween(1, 100),
                    'kpi_id'        => array_rand([1, 2, 13865]),
                    'plan'          => rand(10,300),
                    'share'         => rand(0,100),
                ]
            ];
        }
        foreach ($items as $item)
        {
            DB::table('kpi_items')->insert($item);
        }
    }
}
