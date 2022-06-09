<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\User;
use App\Trainee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Classes\Analytics\Recruiting;

class RecruitingRecordsTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruiting:totals {month?} {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Рекрутинг сводная таблица';

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
        $year = $this->argument('year');
        $month = $this->argument('month');

        if(!$year) $year = date('Y');
        if(!$month) $month = date('m');

        $this->line('== Start ==');
       
        $month = Carbon::createFromFormat('m-Y', $month . '-' . $year)->startOfMonth();

        $helper = new Recruiting();
        $settings = Recruiting::getSummaryTable($month);

        if($settings) {
            $arr = $helper->sumFacts($settings->data, $month);
            $arr = $helper->planRequired($arr);
            
            
            if(Carbon::now()->startOfMonth()->format('Y-m-d') == $month->startOfMonth()->format('Y-m-d')) {
                $settings->extra = $helper->getExtra();
            }

            $settings->data = $arr;
            $settings->save();
            

            $this->line('Таблица найдена и обновлена');
        } else {
            $this->line('Таблица не найдена');
        }

        $this->line('=== End ===');
           
    }


   
    

}
