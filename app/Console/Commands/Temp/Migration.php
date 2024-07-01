<?php

namespace App\Console\Commands\Temp;

use Illuminate\Console\Command;

class Migration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Исправить миграции в BP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            [
                'migration' => '0000_00_00_000000_create_position_table',
            ],
            [
                'migration' => '0000_00_00_000000_create_profile_groups_table',
            ],
            [
                'migration' => '0000_00_00_000000_create_user_descriptions_table',
            ],
            [
                'migration' => '0000_00_00_000000_create_users_table',
            ],
            [
                'migration' => '2022_11_27_163021_create_foreign_keys',
            ],
        ];

        \DB::table('migrations')->insert($data);
    }
}