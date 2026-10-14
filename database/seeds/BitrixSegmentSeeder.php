<?php

namespace Database\Seeders;

use App\Models\Bitrix\Segment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BitrixSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Segment::create([
            "name" => "Кандидаты (hh2)",
            "on_lead" => 3452,
            "on_deal" => 0,
            "active" => 1,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
