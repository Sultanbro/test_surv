<?php

namespace Database\Seeders;

use App\Models\WorkChart\Workday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkdaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workdays = [
            [
                'id'    => 1,
                'name'  => 'Понедельник',
                'short_name' => 'Пн',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 2,
                'name'  => 'Вторник',
                'short_name' => 'Вт',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 3,
                'name'  => 'Среда',
                'short_name' => 'Ср',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 4,
                'name'  => 'Четверг',
                'short_name' => 'Чт',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 5,
                'name'  => 'Пятница',
                'short_name' => 'Пт',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 6,
                'name'  => 'Суббота',
                'short_name' => 'Сб',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 7,
                'name'  => 'Воскресенье',
                'short_name' => 'Вс',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        Workday::query()->insert($workdays);
    }
}
