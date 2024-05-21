<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class WorkingTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('working_times')->insert([
            [
                'id' => 1,
                'name' => '8 часов',
                'time' => 8,
            ],
            [
                'id' => 2,
                'name' => '9 часов',
                'time' => 9,
            ],
        ]);
    }
}
