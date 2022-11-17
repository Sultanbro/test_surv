<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\Service\Department\UserService;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Timetracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timetracking:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запуск проверки таблиц учетам времени';

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
        // Тут проверяем только офисных сотрудниуов, если забудут завершить день то должно авто. завершатся после рабочего дня в группе
        $timetrackings = \App\Timetracking::with('user')->where('exit', null)->get();
        $groups = \App\ProfileGroup::where('active', 1)->get();

        foreach ($timetrackings as $t) {

            // нет юзера пропускаем
            if(!$t->user) {
                continue;
			}

            // юзер в программе юколс пропускаем
            if((int)$t->user->program_id === 1) {
                continue;
            }

            // вычисление таймзоны
            $user_timezone = ($t->user->timezone > 0)?$t->user->timezone:6;
            $tz = \App\Setting::TIMEZONES[$user_timezone];
            $this->line("Для сотрудника с ID " .$t->user_id);
            
            // группы юзера
            $user_groups = $t->user->inGroups();
            
            if ($user_groups->count() > 0) {

                // Выбрать максимальное время окончание работы из груп
                if($t->user && $t->user->work_end) {
                    $max_work_end = $t->user->work_end;
                } else {
                    $max_work_end = $user_groups->max('work_end');
                }
                
                $worktime_end = \Carbon\Carbon::parse($t->enter->format('d.m.Y').' '.$max_work_end, $tz);

                // конец дня с 0 ночи до 9 утра, то смотрим как конец следующий день
                if($worktime_end->hour < 9  && Carbon::now()->setTimezone('Asia/Almaty')->hour >= 9) {
                    $worktime_end->addDays(1);
                }

                // конец рабочего дня
                if ($worktime_end->isPast()) {
                    $t->exit = $worktime_end;
                    $t->save();
                    $this->line("Для сотрудника с ID ".$t->user_id." рабочий день завершен автоматический в ".$worktime_end);
                }
            }

        }
    }
}