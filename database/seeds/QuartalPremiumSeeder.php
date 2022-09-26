<?php

namespace Database\Seeders;

use App\Models\QuartalPremium;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class QuartalPremiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quartalPremiums = [];
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++)
        {
            $quartalPremiums[] = [
                'id'              => $i,
                'targetable_id'   => $faker->numberBetween(1, 100),
                'targetable_type' => array_rand(['App\User', 'App\ProfileGroup', 'App\Position']),
                'activity_id'     => $faker->numberBetween(1, 100),
                'title'           => $faker->word,
                'text'            => $faker->word,
                'plan'            => $faker->numberBetween(100, 300),
                'from'            => date($faker->date()),
                'to'              => date($faker->date())
            ];
        }

        foreach ($quartalPremiums as $quartalPremium)
        {
            DB::table('quartal_premiums')->insert($quartalPremium);
        }
    }
}
