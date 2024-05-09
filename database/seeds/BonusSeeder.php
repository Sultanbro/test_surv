<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class BonusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bonuses = [];
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++)
        {
            $bonuses[] = [
                'id'              => $i,
                'targetable_id'   => $faker->numberBetween(1, 100),
                'targetable_type' => array_random([
                    'App\User',
                    'App\ProfileGroup',
                    'App\Position'
                ]),
                'activity_id'     => $faker->numberBetween(1, 100),
                'group_id'     => $faker->numberBetween(1, 100),
                'title'           => $faker->word,
                'text'            => $faker->word,
                'unit'            => '',
                'quantity'        => $faker->numberBetween(1, 10),
                'daypart'         => $faker->numberBetween(0, 2),
            ];
        }

        DB::table('kpi_bonuses')->insert($bonuses);
    }
}
