<?php

namespace Database\Seeders;

use App\Models\WorkChart\WorkChartModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkChartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $charts = [
            [
                'id'            => 1,
                'name'          => '1-1',
                'start_time'    => '09:00',
                'end_time'      => '19:00',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id'            => 2,
                'name'          => '2-2',
                'start_time'    => '09:00',
                'end_time'      => '19:00',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id'            => 3,
                'name'          => '3-3',
                'start_time'    => '09:00',
                'end_time'      => '19:00',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id'            => 4,
                'name'          => '5-2',
                'start_time'    => '09:00',
                'end_time'      => '19:00',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id'            => 5,
                'name'          => '6-1',
                'start_time'    => '09:00',
                'end_time'      => '19:00',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        WorkChartModel::query()->insert($charts);
    }
}
