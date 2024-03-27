<?php

namespace App\Console\Commands\Payment;

use Illuminate\Console\Command;
use App\Classes\Helpers\Currency;

class RefreshCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить курс валют';

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
        /**
         * API разрешено 1 раз в день
         * currencylayer.com
         */
        Currency::update();
    }
}
