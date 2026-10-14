<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WorkChartTypeRbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table("work_chart_type_rbs")->insert([
            "name" => "обычный",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);

        \DB::table("work_chart_type_rbs")->insert([
            "name" => "сменный",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);
    }
}
