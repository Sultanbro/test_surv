<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class WorkingDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('working_days')->insert([
            [
                'id' => 1,
                'name' => '5-2',
            ],
            [
                'id' => 2,
                'name' => '6-1',
            ],
        ]);
    }
}
