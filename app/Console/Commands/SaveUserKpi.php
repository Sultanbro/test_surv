<?php

namespace App\Console\Commands;

use App\Events\KpiChangedEvent;
use App\Kpi;
use App\SavedKpi;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Service\KpiStatisticService;
use App\Service\CalculateKpiService;
use Illuminate\Http\Request;

class SaveUserKpi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:save_kpi {date?} {user_id?}';  //php artisan user:save_kpi 2022-08-01 // целый месяц , долго


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
     * Рабочие дни
     */
    public array $workdays;

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

        // count workdays in month
        $this->workdays = [];
        $this->workdays[5] = workdays(Carbon::parse($date)->year, Carbon::parse($date)->month, [6,0]);
        $this->workdays[6] = workdays(Carbon::parse($date)->year, Carbon::parse($date)->month, [0]);

        /**
         * working users not trainees
         */
        $users =  \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where(function($query) use ($date){
                $query->whereDate('deleted_at', '>=', $date)
                    ->orWhereNull('deleted_at');
            })
            ->where('is_trainee', 0);


        if($this->argument('user_id')) {
            $users->where('users.id', $this->argument('user_id'));
        }

        $users = $users->select(['users.id','users.last_name', 'users.name'])
            ->get(['users.id']);

        $this->comment($users->count());

        /**
         * Calc users kpi by order
         */


        foreach ($users as $key => $user) {

            $this->line($key . ' '. $user->id);

            // fetch kpis of user
            $repo = $this->repo->fetchKpis(new Request([
                'filters' => [
                    'data_from' => [
                        'month' => Carbon::parse($date)->month,
                        'year'  => Carbon::parse($date)->year,
                    ],
                    'user_id'   => $user->id
                ]
            ]));


            // save
            $this->updateSavedKpi([
                'user_id' => $user->id,
                'date'    => $date,
                'total'   => $this->calc($repo['items']),
            ]);

        }

    }

    /**
     * calc kpis of user
     */
    private function calc($kpis) : float
    {
        $earned = 0;

        foreach ($kpis as $key => $kpi) {
            if(!isset($kpi['users'][0])) continue;
//            dd($kpi['users'][0]['items']);
//             dd($kpi['users'][0]['items'][2]);
            foreach ($kpi['users'][0]['items'] as $item) {

                $itemActivityWeekdays = (int) ($item['activity']['weekdays'] ?? 5);

                $workdays = $itemActivityWeekdays == 0
                    ? $this->workdays[5]
                    : $this->workdays[$itemActivityWeekdays];

                $completed_percent = $this->calculator->getCompletePercent([
                    'fact' => $item['fact'],
                    'avg' => $item['avg'],
                    'records_count' => $item['records_count'],
                    'daily_plan' => (int)$item['daily_plan'],
                    'full_time' => $item['full_time'],
                    'days_from_user_applied' => 0,
                    'workdays' => $workdays,
                ], $item['method']);

                if(
                    //!$item['allow_overfulfillment']
                    $completed_percent > 100) {
                    $completed_percent = 100;
                }

                $earned += $this->calculator->earned(
                    $kpi['lower_limit'],
                    $kpi['upper_limit'],
                    $completed_percent,
                    $item['share'],
                    $item['full_time'] == 1 ? $kpi['completed_80'] : $kpi['completed_80'] / 2,
                    $item['full_time'] == 1 ? $kpi['completed_100'] : $kpi['completed_100'] / 2,
                );


                // dump($kpi['lower_limit'],
                // $kpi['upper_limit'],
                // $completed_percent,
                // $item['share'],
                // $kpi['completed_80'],
                // $kpi['completed_100']);
                dump($earned);

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

        $date = null;
        if($sk) {
            $sk->total = $data['total'];
            $sk->save();
            $date = $sk->date;
        } else {
            SavedKpi::create($data);
            $date = $data['date'];
        }

        if($date){
            $date = Carbon::createFromFormat('Y-m-d', $data['date']);
            event(new KpiChangedEvent($date));
        }
    }

}
