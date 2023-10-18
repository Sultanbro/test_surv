<?php

namespace App\Console\Commands\Trainee;

use Illuminate\Console\Command;
use App\ProfileGroup;
use App\DayType;
use Carbon\Carbon;
use App\Models\Bitrix\Lead;
use App\Models\Analytics\TraineeReport;

class CreateTraineeReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruiting:trainee_report {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Таблица для хранения отчета о присутствии стажеров с 1 по 7 день обучения и ответы для оценки обучения';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->date = date('Y-m-d'); 
        $this->year = date('Y');
        $this->month = date('m');
        $this->day = date('d');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    public $date; 
    public $year;
    public $month;
    public $day;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->prepare();



        $groups = ProfileGroup::where('active', 1)->get();


        $dates = [
            Carbon::now()->format('Y-m-d'),
            Carbon::now()->subDays(1)->format('Y-m-d'),
            Carbon::now()->subDays(2)->format('Y-m-d'),
            Carbon::now()->subDays(2)->format('Y-m-d'),
            Carbon::now()->subDays(3)->format('Y-m-d'),
            Carbon::now()->subDays(4)->format('Y-m-d'),
            Carbon::now()->subDays(5)->format('Y-m-d'),
            Carbon::now()->subDays(6)->format('Y-m-d'),
            Carbon::now()->subDays(7)->format('Y-m-d'),
            Carbon::now()->subDays(8)->format('Y-m-d'),
        ];

        foreach($groups as $group) {


            foreach ($dates as $date) { // last 8 days

                $arr = [];
                $arr['group_id'] = $group->id;
                $arr['date'] = $date;

             
                $leads = Lead::whereDate('invite_at', $date)
                    ->where('invite_group_id', $group->id)
                    ->whereIn('user_id', $group->trainees($date))
                    ->get()
                    ->pluck('user_id')
                    ->toArray();
                
                    $arr['leads'] = count($leads);
            
                $next_dates = $this->getNextDates($date);
                foreach ($next_dates as $nd_index => $next_date) { // days after invite day
                    $training_days = DayType::whereIn('type', [5,7])
                        ->whereIn('user_id', $leads)
                        ->where('date', $next_date)
                        ->get();

                    $arr['day_'. ($nd_index + 1)] = $training_days->count();
                }
                
                //dump($next_dates);
                $this->saveArr($arr);
            }
        }

    }

    /**
     * prepare dates
     */
    public function prepare()
    {
        if($this->argument('date')) $this->date = $this->argument('date');

        $datex = explode("-", date("Y-m-d", strtotime($this->date)));
        $this->year = $datex[0];
        $this->month = $datex[1];
        $this->day = $datex[2];

        // $dates = [
        //     Carbon::now(),
        //     Carbon::now()->subDays(7)->format('Y-m-d'),
        //     Carbon::now()->subDays(14)->format('Y-m-d'),
        //     Carbon::now()->subDays(21)->format('Y-m-d'),
        //     Carbon::now()->subDays(28)->format('Y-m-d'),
        //     Carbon::now()->subDays(35)->format('Y-m-d'),
        // ];

        // foreach($dates as $d) {
        //     $datex = explode("-", $d);
        //     $this->year = $datex[0];
        //     $this->month = $datex[1];
        //     $this->day = $datex[2];

        //     $this->getData();

        // }
    }

    /**
     * save arr to TraineeReport
     */
    public function saveArr($arr)
    {
        $tr = TraineeReport::where('date', $arr['date'])
            ->where('group_id', $arr['group_id'])
            ->first();

        if($tr) {
            $tr->leads = array_key_exists('leads', $arr) ? $arr['leads'] : 0;
            $tr->day_1 = array_key_exists('day_1', $arr) ? $arr['day_1'] : 0;
            $tr->day_2 = array_key_exists('day_2', $arr) ? $arr['day_2'] : 0;
            $tr->day_3 = array_key_exists('day_3', $arr) ? $arr['day_3'] : 0;
            $tr->day_4 = array_key_exists('day_4', $arr) ? $arr['day_4'] : 0;
            $tr->day_5 = array_key_exists('day_5', $arr) ? $arr['day_5'] : 0;
            $tr->day_6 = array_key_exists('day_6', $arr) ? $arr['day_6'] : 0;
            $tr->day_7 = array_key_exists('day_7', $arr) ? $arr['day_7'] : 0;
            $tr->save();
        } else {
            TraineeReport::create([
                'date' => $arr['date'],
                'group_id' => $arr['group_id'],
                'leads' => $arr['leads'],
                'day_1' => array_key_exists('day_1', $arr) ? $arr['day_1'] : 0,
                'day_2' => array_key_exists('day_2', $arr) ? $arr['day_2'] : 0,
                'day_3' => array_key_exists('day_3', $arr) ? $arr['day_3'] : 0,
                'day_4' => array_key_exists('day_4', $arr) ? $arr['day_4'] : 0,
                'day_5' => array_key_exists('day_5', $arr) ? $arr['day_5'] : 0,
                'day_6' => array_key_exists('day_6', $arr) ? $arr['day_6'] : 0,
                'day_7' => array_key_exists('day_7', $arr) ? $arr['day_7'] : 0,
            ]);
        }
    }

    public function getNextDates($date)
    {
        $arr = [$date];

        $carbon = Carbon::parse($date);
        for($i = 1; $i <=8; $i++) {
            $carbon->addDays(1);
            if($carbon->timestamp < time() && !in_array($carbon->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                array_push($arr, $carbon->format('Y-m-d'));
            }
        }

        return $arr;
    }
}