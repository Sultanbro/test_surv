<?php

namespace App\Console\Commands;

use App\User;
use App\Http\Controllers\Admin\AnvizController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchAnvizRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:anviz {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Anviz Time attendance records from Sql server (bp_anviz) DB';

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
    public function handle() {
        $argDate = $this->argument('date');
        $date = date('Y-m-d');
        if (!is_null($argDate)) $date = $argDate;

        $controller = new AnvizController();
		$controller->checkInOut($date);
    }
}
