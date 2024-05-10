<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwardTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'name' => 'Номинаций',
                'description' => 'Получает при выполнений плана.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Сертификаты',
                'description' => 'Получает при успешной прохождений курсов.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Начисления',
                'description' => 'начисления',
                'created_at' => now(),
                'updated_at' => now()
            ],

        ];

        DB::table('award_types')->insert($types);
    }
}
