<?php

namespace Database\Seeders;

use DB;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;

class JobtronSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function run(): void
    {
        $sql = File::get(database_path('sql/jobtron-dump.sql'));
        DB::connection('mysql')->unprepared($sql);
    }
}
