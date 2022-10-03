<?php

namespace App\Console\Commands\Callibro;

use Illuminate\Console\Command;
use App\User;
use App\Timetracking;
use App\TimetrackingHistory;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Classes\Analytics\Eurasian;
use App\Classes\Analytics\Kaztel;
use App\Classes\Analytics\Euras2;
use App\Classes\Analytics\HomeCredit;
use App\Models\Analytics\UserStat;

class Conversion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callibro:conversion {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calc aggrees conversion and save on activity table of group';

     /**
     * 
     */
    protected $day;

    /**
     * 'Y-m-d'
     */
    protected $date;

    /**
     * 'Y-m-d'
     */
    protected $startOfMonth;

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
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();
        $this->date = $this->argument('date') ? $this->argument('date') : date('Y-m-d');
        $this->day = $date->day;
        $this->startOfMonth = $date->startOfMonth()->format('Y-m-d');

        $groups = [53];
        foreach($groups as $group_id) {
            $this->fetch($group_id);
            $this->line('Fetch completed for group_id: ' . $group_id);
        }
    }

    public function fetch($group_id) {

        $users_ids = json_decode(ProfileGroup::find($group_id)->users);
        $users = User::whereIn('id', $users_ids)->get();

        foreach($users as $user) {
           
            if($group_id == 53) { // Eurasian
                $closed = Eurasian::getClosedCards($this->date,$user->email);
                if($closed == -1) continue; // Не записывать так как нет аккаунта

                $aggrees = Eurasian::getAggrees($user->email, $this->date);

                $this->line($user->id . ' '.  $user->last_name . ' ' . $user->name);

                $conversion = 0; 
                if($aggrees != 0) {
                    $conversion = $aggrees / $closed * 100;
                    $conversion = number_format($conversion, 1);
                }

                $this->line('Согласий  '. $aggrees);
                $this->line('Закрыто   '. $closed);
                $this->line('Конверсия '. $conversion);

                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 65 // Конверсия согласий
                ], $conversion);

                $this->line('Сохранено');
                $this->line(' '); 
            }

        }

    }

    public function saveASI(array $fields, $value) {
      
         // User stat New analytics
        $date = Carbon::parse($fields['date'])->day($this->day)->format('Y-m-d');
        $us = UserStat::where([
            'date' => $date,
            'user_id' => $fields['employee_id'],
            'activity_id' => $fields['type'] 
        ])->first();

        if($us) {
            $us->value = $value;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $fields['employee_id'],
                'activity_id' => $fields['type'],
                'value' => $value
            ]);
        }

    }
}
