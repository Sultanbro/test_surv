<?php

namespace App\Console\Commands;

use App\Kpi;
use App\SavedKpi;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Service\KpiStatisticService;
use App\Service\CalculateKpiService;

class SaveUserKpi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:save_kpi {date?}';  //php artisan user:save_kpi 2022-08-01 // целый месяц , долго
                                                             

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сохранить kpi';

    /**
     * Variables that used 
     *
     * @var mixed
     */
    public $date; // Дата пересчета 
    
    /**
     * Вытащить кпи показатели сотрудника
     *
     * @var KpiStatisticService
     */
    public $repo; 

    /**
     * Расчет выполнеия и суммы кпи
     *
     * @var CalculateKpiService
     */
    public $calculator; 

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->repo = new KpiStatisticService();
        $this->calculator = new CalculateKpiService();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = $this->argument('date') ?? date('Y-m-d');
        $date = Carbon::parse($date)->day(1)->format('Y-m-d');

        /**
         * working users not trainees
         */
        $users =  \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->select(['users.id','users.last_name', 'users.name'])
            ->get();
   
        $this->comment($users->count());
        
        /**
         * Calc users kpi by order
         */
        foreach ($users as $key => $user) {

            $this->line($key . ' '. $user->id);

            // fetch kpis of user
            $kpis = $this->repo->fetchKpis([
                'data_from' => [
                    'month' => Carbon::parse($date)->month,
                    'year'  => Carbon::parse($date)->year,
                ], 
                'user_id'   => $user->id
            ]);

            // save
            $this->updateSavedKpi([
                'user_id' => $user->id,
                'date'    => $date,
                'total'   => $this->calc($kpis),
            ]);

        }

    }

    /**
     * calc kpis of user
     */
    private function calc(array $kpis) : float
    {
        $earned = 0;

        foreach ($kpis as $key => $kpi) {

            foreach ($kpi->users[0]->items as $item) {
                
                $completed_percent = $this->calculator->getCompletePercent([
                    'fact' => $item['fact'],
                    'daily_plan' => $item['plan'],
                    'full_time' => $item['full_time'],
                    'days_from_user_applied' => 0,
                    'workdays' => 0,
                ], $item->method);

                $earned += $this->calculator->earned(
                    $kpi['lower_limit'],
                    $kpi['upper_limit'],
                    $completed_percent,
                    $item['share'],
                    $kpi['completed_80'],
                    $kpi['completed_100']
                );
            }
        }

        return $earned;
    }

    /**
     * save kpi
     */
    private function updateSavedKpi(array $data) : void
    {
        // save 
        $sk = SavedKpi::where('user_id', $data['user_id'])
            ->where('date', $data['date'])
            ->first();

        if($sk) {
            $sk->update($data);
        } else {
            SavedKpi::create($data);
        }
    }

}
