<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kpis =  [
            [
                'id'              => 1,
                'targetable_id'   => 5,
                'targetable_type' => 'App\User',
                'completed_80'    => 80,
                'completed_100'   => 100,
                'lower_limit'     => 80,
                'upper_limit'     => 100,
                'colors'          => json_encode([
                    'red'   => 50,
                    'green' => 80
                ])
            ],
            [
                'id'              => 2,
                'targetable_id'   => 13685,
                'targetable_type' => 'App\User',
                'completed_80'    => 80,
                'completed_100'   => 100,
                'lower_limit'     => 80,
                'upper_limit'     => 100,
                'colors'          => json_encode([
                    'yellow'   => 20,
                    'green' => 80
                ])
            ],
            [
                'id'              => 3,
                'targetable_id'   => 26,
                'targetable_type' => 'App\ProfileGroup',
                'completed_80'    => 80,
                'completed_100'   => 100,
                'lower_limit'     => 80,
                'upper_limit'     => 100,
                'colors'          => json_encode([
                    'red'   => 50,
                    'green' => 80
                ])
            ],
        ];

        foreach ($kpis as $kpi)
        {
            DB::table('kpis')->insert($kpi);
        }
    }
}
