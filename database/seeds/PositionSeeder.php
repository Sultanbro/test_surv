<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('tenant')->table('position')->insert([[
            'id' => 1,
            'book_groups' => null,
            'position' => 'Владелец',
            'indexation' => 0,  
            'sum' => 0
        ]]);
    }
}
