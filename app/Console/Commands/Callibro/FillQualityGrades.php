<?php

namespace App\Console\Commands\Callibro;

use Illuminate\Console\Command;
use App\Classes\Callibro;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\QualityRecordWeeklyStat;
use App\QualityRecordMonthlyStat;

class FillQualityGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callibro:grades {date?}';

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
                $account = new Callibro($user->EMAIL);
                if(!$account->account) continue;

                $this->line($user->LAST_NAME . ' ' . $user->NAME);
                $grades = $account->call_grades([
                    'date' => $this->date,
                    'dialer_id' => $group['dialer_id'],
                ], $group['script_grades'])->groupBy('call_id');

                
                $total = 0;
                $count = 0;

                foreach($grades as $grade) {
                    $total += (int)$grade->sum('value');
                    $count++;
                }

                if($count > 0) {
                    $avg = (int)round($total / $count);
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
        $user_ids = json_decode(ProfileGroup::find($group_id)->users);

        return User::withTrashed()
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'b_user.ID')
                ->where('UF_ADMIN', 1)
                ->where('is_trainee', 0)
                ->whereIn('b_user.ID', $user_ids)
                ->select(['b_user.ID', 'b_user.EMAIL', 'b_user.NAME', 'b_user.LAST_NAME'])
                ->get();
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
    }

    /**
     * Prepare start array
     */
    private function setGroups() {
        return [
            [
                'name' => 'Евраз',
                'group_id' => 53,
                'dialer_id' => 398,
                'script_id' => 2508,
                'script_grades' => [],
            ],
        ];
    }

}
