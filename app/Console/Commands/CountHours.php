<?php

namespace App\Console\Commands;

use App\Components\TelegramBot;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CountHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:hours {date?} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $argDate1 = $this->argument('date');
        $argUserId = $this->argument('user_id');

        $timeZone = Setting::TIMEZONES[6];
        if (!is_null($argDate1)) {
            $date2 = Carbon::parse($argDate1, $timeZone)->startOfDay();
        } else {
            $date2 = Carbon::now($timeZone)->startOfDay();
        }
        $this->line("Дата ".$date2);

        $date = explode(".", date("d.m.Y", strtotime($date2)));
        $day = $date[0];
        $month = $date[1];
        $year = $date[2];


        if (!is_null($argUserId)) {
            // команда запускается, теперь надо посчитать минуты от одной даты до другой
            $timetrackingDays = DB::table('timetracking')
                ->select('id', 'enter','exit','total_hours','user_id', 'created_at', 'updated')
                ->where('user_id', '=', $argUserId)
                ->whereDay('enter', '=', $day)
                ->whereMonth('enter', '=', $month)
                ->whereYear('enter', '=', $year)
                ->get();

            foreach ($timetrackingDays as $day) {
                // теперь надо посчитать минуты
                $diffInSeconds = round(strtotime($day->exit) - strtotime($day->enter));
                $diffInMinutes = round($diffInSeconds/60);
                DB::table('timetracking')->where('id', '=', $day->id)->update(['total_hours' => $diffInMinutes]);
                $this->line('Было обновлено '. $diffInMinutes. ' минут');
            }
        } else {
            $timetrackingUsers = DB::table('timetracking')
                ->select('id', 'enter', 'exit', 'total_hours', 'user_id', 'created_at', 'updated')
                ->whereDay('enter', '=', $day)
                ->whereMonth('enter', '=', $month)
                ->whereYear('enter', '=', $year)
                ->get();

            $userGroups = DB::table('profile_groups')->get();
//            TelegramBot::send($userGroups);

            foreach($timetrackingUsers as $user) {
//                TelegramBot::send($user);

                // вот здесь мне надо брать либо время профиля, либо время группы
                $userProfile = DB::table('b_user')
                    ->select('*')
                    ->where('id', '=', $user->user_id)
                    ->first();

                if (!is_null($userProfile)) {

                    $workStart = '09:00:00';
                    if (!is_null($userProfile->work_start)) {
                        $workStart = $userProfile->work_start;
                        $this->line("ID пользователя ".$user->user_id);
//                        TelegramBot::send("Время из профиля ".$workStart);
                    } else {


                        foreach ($userGroups as $group) {
                            $usersInGroup = explode(',', trim($group->users, '[]'));
                            foreach ($usersInGroup as $userIDInGroup) {
                                if ($user->user_id == $userIDInGroup ) {
                                    $workStart = $group->work_start;
                                    break;
                                }

                            }
                        }
                        // TelegramBot::send("Время из группы ". $workStart);
                    }
                    $timeStart = $user->enter;
                    $timeEnd = $user->exit;

                    $workStartInSeconds = strtotime($year.'-'.$month.'-'.$day.' '.$workStart);
                    //  $this->line("Время начала смены ". $year.'-'.$month.'-'.$day.' '.$workStart);
                    //  $this->line("Время начала работы ". $user->enter);
                    //  $this->line("Время конца работы ". $user->exit);
                    if ($workStartInSeconds > strtotime($user->enter) && $user->updated == 0) {
                        $this->line("Время начала работы меньше чем время начала смены, значит считаем от начала смены");
                        $timeStart = $year.'-'.$month.'-'.$day.' '.$workStart;
                        //TelegramBot::send('timeStart = '.$timeStart);
                    }

                    if($user->updated == 1) {
                        $timeStart = $year.'-'.$month.'-'.$day.' '.$workStart;
                    }

                    //$this->line("Итого считает работу от ". $timeStart);
                    //$this->line("до ". $timeEnd);
                    $diffInSeconds = round(strtotime($timeEnd) - strtotime($timeStart));
                    $diffInMinutes = round($diffInSeconds/60);
                    $diffInHours = round(floatval($diffInMinutes/60),2);
                    //                    $this->line("Отработано минут ". $diffInMinutes);
                    //                    $this->line("Отработано часов ". $diffInHours);

                    //TelegramBot::send('diffInHours = '.$diffInHours);

                    $lunch = 1;
                    if ($user->updated === 0) {
                        if ($diffInHours > 5) {
                            $diffInHours = $diffInHours - $lunch;
                            //TelegramBot::send('minus lunch = '.$diffInHours);
                        }
                    }
                    
                    if($user->updated == 1) {
                        if ($diffInHours > 5) {
                            $diffInHours = $diffInHours - $lunch;
                            //TelegramBot::send('minus lunch = '.$diffInHours);
                        }
                    }


                    $diffInMinutes = $diffInHours * 60;
                    //TelegramBot::send('diffInMinutes = '.$diffInMinutes);
                    if ($diffInMinutes <= 0) {
                        $diffInMinutes = 0;
                    }


                    if ($user->updated === 0) {
                        DB::table('timetracking')->where('id', '=', $user->id)->update(['total_hours' => $diffInMinutes]);
                        //$this->line('Было обновлено '. $diffInMinutes. ' минут, у пользователя '.$user->id);
                    }

                    // if($user->updated == 1) {
                    //     DB::table('timetracking')->where('id', '=', $user->id)->update(['total_hours' => $diffInMinutes]);
                    // }
                    
                    
                }

            }
            $this->line('Было найдено юзеров: '. count($timetrackingUsers));

        }

    }
}
