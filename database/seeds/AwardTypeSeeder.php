<?php

namespace Database\Seeders;

use App\Models\AwardType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'Сертификаты',
                'description' => 'Получает при успешной прохождений курсов.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Номинаций',
                'description' => 'Получает при выполнений плана.',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('award_types')->insert($types);
    }
}
