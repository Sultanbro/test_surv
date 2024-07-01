<?php

namespace App\Console\Commands;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MonthHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'month:hours {date?} {user_id?}';

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

        $timeZone = Setting::TIMEZONES[5];
        if (!is_null($argDate1)) {


            $this->line("Дата ".$argDate1);
            $date = explode(".",$argDate1);
            $month = $date[0];
            $year = $date[1];

            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $this->line("Кол-во дней ".$days);

            for ($i = 0; $i <= $days; $i++) {

                $day = $i;
                $this->line("Дата ".$day.".".$month.".".$year);

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
                }
                else {
                    $timetrackingUsers = DB::table('timetracking')
                        ->select('id', 'enter', 'exit', 'total_hours', 'user_id', 'created_at', 'updated')
                        ->whereDay('enter', '=', $day)
                        ->whereMonth('enter', '=', $month)
                        ->whereYear('enter', '=', $year)
                        ->get();

                    $userGroups = DB::table('profile_groups')->get();

                    foreach($timetrackingUsers as $user) {

                        // вот здесь мне надо брать либо время профиля, либо время группы
                        $userProfile = DB::table('users')
                            ->select('*')
                            ->where('id', '=', $user->user_id)
                            ->first();

                        if (!is_null($userProfile)) {


                            if (!is_null($userProfile->work_start)) {
                                $workStart = $userProfile->work_start;
                                $this->line("ID пользователя ".$user->user_id);
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
                            }
                            $timeStart = $user->enter;
                            $timeEnd = $user->exit;


                            $workStartInSeconds = strtotime($year.'-'.$month.'-'.$day.' '.$workStart);
        //                    $this->line("Время начала смены ". $year.'-'.$month.'-'.$day.' '.$workStart);
        //                    $this->line("Время начала работы ". $user->enter);
        //                    $this->line("Время конца работы ". $user->exit);
                            if ($workStartInSeconds > strtotime($user->enter) && $user->updated == 0) {
                                $this->line("Время начала работы меньше чем время начала смены, значит считаем от начала смены");
                                $timeStart = $year.'-'.$month.'-'.$day.' '.$workStart;
                            }
                            $this->line("Итого считает работу от ". $timeStart);
                            $this->line("до ". $timeEnd);
                            $diffInSeconds = round(strtotime($timeEnd) - strtotime($timeStart));
                            $diffInMinutes = round($diffInSeconds/60);
                            $diffInHours = round(floatval($diffInMinutes/60),2);
        //                    $this->line("Отработано минут ". $diffInMinutes);
        //                    $this->line("Отработано часов ". $diffInHours);



                            $lunch = 1;
                            if ($user->updated === 0) {
                                if ($diffInHours > 5) {
                                    $diffInHours = $diffInHours - $lunch;
                                }
                            }
                            $diffInMinutes = $diffInHours * 60;

                            if ($diffInMinutes <= 0) {
                                $diffInMinutes = 0;
                            }


                            DB::table('timetracking')->where('id', '=', $user->id)->update(['total_hours' => $diffInMinutes]);
                            $this->line('Было обновлено '. $diffInMinutes. ' минут, у пользователя '.$user->id);
                        }

                    }
                    $this->line('Было найдено юзеров: '. count($timetrackingUsers));

                }
            }
        } else {
            $this->line("Пример команды php artisan month:hours 10.2020");
        }

    }

}
