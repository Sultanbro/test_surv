<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $awards = [];
        $faker = Faker::create();

        for ($i = 0; $i <= 10; $i++)
        {
            $awards[] = [
                'award_type_id' => $faker->numberBetween(1,2),
                'format' => array_random(['PDF', 'JPG', 'PNG']),
                'path' => 'url',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('awards')->insert($awards);
    }
}
