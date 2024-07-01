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
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        /**
         * API разрешено 1 раз в день
         * currencylayer.com
         */
        Currency::update();
    }
}
