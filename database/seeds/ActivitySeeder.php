<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [];
        $faker = Faker::create();

        for ($i = 1; $i <= 2; $i++)
        {
            $items[] = [
                'id'              => $i,
                'name'            => $faker->word,
                'group_id'        => 1,
                'daily_plan'      => $faker->numberBetween(1, 100),
                'share'     => $faker->numberBetween(1, 100),
                'unit'            => '',
                'method'           => $faker->numberBetween(1, 6),
                'view'        => $faker->numberBetween(0, 6),
                'source'         => $faker->numberBetween(0, 3),
                'editable'            => 1,
                'order'            => 1,
                'weekdays'            => 5,
            ];
        }

        DB::table('activities')->insert($items);
    }
}
