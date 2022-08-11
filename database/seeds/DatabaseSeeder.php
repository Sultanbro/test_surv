<?php

use Database\Seeders\KpiItemSeeder;
use Database\Seeders\KpiSeeder;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             KpiSeeder::class,
             KpiItemSeeder::class
         ]);
    }
}
