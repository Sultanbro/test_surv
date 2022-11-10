<?php

namespace App\Console\Commands\Callibro;

use Illuminate\Console\Command;
use App\Classes\Callibro;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\QualityRecordWeeklyStat;
use App\QualityRecordMonthlyStat;
use App\Models\CallibroDialer;
use App\Models\Analytics\Activity;
use App\Models\Analytics\UserStat;
use App\Service\Department\UserService;

class FillQualityGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callibro:grades {date?} {fired?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill weekly quality grades';

    /**
     * 
     */
    protected $day;

    /**
     * 
     */
    protected $month;

    /**
     * 
     */
    protected $year;

    /**
     * 'Y-m-d'
     */
    protected $date;

    /**
     * 'Y-m-d'
     */
    protected $startOfMonth;

    /**
     * Массив с группами
     */
    protected $groups;

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
        $this->line('Prepare data');
        $this->prepare();

        foreach($this->groups as $group) {
            $group['script_grades'] = Callibro::script_grades($group['script_id']);
            $users = $this->getUsers($group['group_id']);

            $this->line('========');
            $this->line($group['name']);
            $this->line('========');
            foreach($users as $user) {
                $account = new Callibro($user->email);
                if(!$account->account) continue;

                $this->line($user->last_name . ' ' . $user->name);
                $grades = $account->call_grades([
                    'date' => $this->date,
                    'dialer_id' => $group['dialer_id'],
                ], $group['script_grades'])->groupBy('call_id');

                //if($user->id == 18675) dd($grades);
                $total = 0;
                $count = 0;

                foreach($grades as $grade) {
                    $total += (int)$grade->sum('value');
                    $count++;
                }

                if($count > 0 && $count != 1) {
                    $avg = (int)round($total / ($count-1));
                    if($avg != 0) {
                        $this->saveQualityRecord($user->id, $avg, $group['group_id']);
                    }
                }
            }
        }
    }

    /**
     * Prepare properties
     */
    private function prepare() {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();
        $this->date = $this->argument('date') ? $this->argument('date') : date('Y-m-d');
        $this->day = $date->day;
        $this->month = $date->month;
        $this->year = $date->year;
        $this->startOfMonth = $date->startOfMonth()->format('Y-m-d');
        $this->groups = $this->setGroups(); // Евраз, Хоум
    }

    /**
     * Get users collection
     */
    private function getUsers($group_id) {

        $users = collect((new UserService)->getEmployees(
            $group_id,
            Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')
        )); 

        $trainees = (new UserService)->getTrainees(
            $group_id,
            Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')
        ); 

        $users = $users->merge(collect($trainees));
        
        if($this->argument('fired')) {
            $fired = (new UserService)->getFiredEmployees(
                $group_id,
                Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')
            ); 
            
            $users = $users->merge(collect($fired));
        }

        return $users;
    }

    /**
     * Save grade to QualityRecordWeeklyStat::class 
     */
    private function saveQualityRecord($user_id, $grade, $group_id) {
        $qr = QualityRecordWeeklyStat::where([
            'day' => $this->day,
            'month' => $this->month,
            'year' => $this->year,
            'user_id' => $user_id,
            'group_id' => $group_id,
        ])->first();

        if($qr) {
            $qr->total = (int)$grade;
            $qr->save();
        } else {
            QualityRecordWeeklyStat::create([
                'day' => $this->day,
                'month' => $this->month,
                'year' => $this->year,
                'user_id' => $user_id,
                'total' => (int)$grade,
                'group_id' => $group_id,
            ]);
        }

        // save user_stats
        UserStat::saveQuality([
            'date'     => $this->date,
            'user_id'  => $user_id,
            'value'    => (int)$grade,
            'group_id' => $group_id,
        ]);
    }

    /**
     * Prepare start array
     */
    private function setGroups() {

        $groups = ProfileGroup::where('active', 1)
            ->where('quality', 'ucalls')
            ->with('dialer')
            ->has('dialer')
            ->get();

        $arr = [];

        foreach($groups as $group) {
            $arr[] = [
                'name' => $group->name,
                'group_id' => $group->id,
                'dialer_id' => $group->dialer->dialer_id,
                'script_id' => $group->dialer->script_id, // 2508 - eurasian bank
                'script_grades' => [],
            ];
        }
        
        return $arr;
    }

}
