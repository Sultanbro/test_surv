<?php

namespace App\Console\Commands\Employee;

use Illuminate\Console\Command;
use App\Models\Admin\Bonus;
use App\Models\Admin\ObtainedBonus;
use App\ProfileGroup;
use App\ProfileGroupUser;
use App\User;
use App\Salary;
use App\UserNotification;
use App\Models\User\NotificationTemplate;
use Carbon\Carbon;

class BonusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:update {date?} {group_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update employee bonuses for today';


    /**
     *
     */
    protected $date;

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
     * handle
     */
    public function handle() {

        if($this->argument('date')) {
            $date = Carbon::parse($this->argument('date'));
            $this->date =  $this->argument('date');
            $this->line('Начат пересчет бонусов на ' . $this->date);
            $this->update();
            $this->line('Закончил расчет за '. $this->date);

            $this->notify($date);
        } else {

            $days = [
                Carbon::now()->subDays(7)->format('Y-m-d'),
                Carbon::now()->subDays(6)->format('Y-m-d'),
                Carbon::now()->subDays(5)->format('Y-m-d'),
                Carbon::now()->subDays(4)->format('Y-m-d'),
                Carbon::now()->subDays(3)->format('Y-m-d'),
                Carbon::now()->subDays(2)->format('Y-m-d'),
                Carbon::now()->subDays(1)->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ];

            foreach ($days as $day) {
                $this->date = $day;
                $this->line('Начат пересчет бонусов на ' . $this->date);
                $this->update();
                $this->line('Закончил расчет за '. $day);
            }



            //$this->notify(date('Y-m-d'));
        }



    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function update()
    {
        /**
         * ГРуппы
         */
        if($this->argument('group_id')) {
            $groups = ProfileGroup::where('active', 1)
                ->where('id', $this->argument('group_id'))
                ->get(['name', 'id']);
        } else {
            $groups = ProfileGroup::where('active', 1)->get(['name', 'id']);
            $this->line('Найдено ' . $groups->count());
        }

        if($this->argument('group_id')) {
            /**
             * Выбрать пользователей
             */
            $group = ProfileGroup::find($this->argument('group_id'));
            $user_ids = json_decode($group->users);
            $user_ids = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', $user_ids)
                ->where('is_trainee', 0)
                ->get(['users.id'])->pluck('id')->toArray();

            // Обнулить award
            $salaries = Salary::where('date', $this->date)
                ->whereIn('user_id', $user_ids)
                ->update([
                    'award' => 0
                ]);
        } else {
            // Обнулить award
            $salaries = Salary::where('date', $this->date)->update([
                'award' => 0
            ]);
        }


        /**
         * цикл по группам
         */
        foreach($groups as $group) {
            $this->comment($group->name);

            /** Пересчет бонусов */
            $awards = Bonus::obtained_in_group($group->id, $this->date);

            //dump($awards);
        }

    }


    /**
     * Update notification about bonuses
     */
    public function notify($date) {
        $this->comment('Уведомления :');
        $timestamp = now();
        $noti = NotificationTemplate::find(15); // Сегодня получили бонусы


        foreach (json_decode($noti->ids) as $key => $group_id) {

            $this->line('Notify: ' . $group_id);
            $msg = $this->createNotificationMessage($group_id, $date);

            if($msg == 'No bonuses') {
                $this->line( $group_id . ' NO Bonuses ');
                continue;
            }

            $pgu =  ProfileGroupUser::where('group_id', $group_id)
                ->where('date', Carbon::now()->startOfMonth()->format('Y-m-d'))
                ->first();

            if($pgu) {
                $notification_receivers = $pgu->assigned;

                foreach ($notification_receivers as $key => $user_id) {

                    $noti = UserNotification::where('user_id', $user_id)
                        ->where('title', 'Сегодня получили бонусы')
                        ->whereDate('created_at', date('Y-m-d'))
                        ->first();

                    if($noti) {
                        $noti->message = $msg;
                        $noti->note = $group_id;
                        $noti->save();
                    } else {
                        UserNotification::create([
                            'user_id' => $user_id,
                            'about_id' => 0,
                            'title' => 'Сегодня получили бонусы',
                            'group' => now(),
                            'message' => $msg,
                            'note' => $group_id,
                        ]);
                    }


                }
            }

            $this->line($group_id . ' Users have notified');

        }

    }

    public function createNotificationMessage($group_id, $date)
    {
        $msg = $date;
        $msg .= '<br>';

        $kpi_bonuses = Bonus::where('group_id', $group_id)->get()->pluck('id')->toArray();

        if($group_id == 42) {
            $bonus_ids = ObtainedBonus::where('date', $date)
                ->where('amount', '>', 0)
                ->whereIn('bonus_id', $kpi_bonuses)
                ->get()
                ->groupBy('bonus_id');

            $has_text = false;


            foreach($bonus_ids as $bonus_id => $bonuses) {

                $best_bonus = null;
                foreach($bonuses as $bonus) {
                    if(!$best_bonus) {
                        $best_bonus = $bonus;
                    } else if ($best_bonus->amount < $bonus->amount){
                        $best_bonus = $bonus;
                    }
                }

                if($best_bonus) {
                    $has_text = true;
                    $user = User::withTrashed()->find($best_bonus->user_id);
                    if($user) {
                        $msg .= '<b>' . $user->name . ' ' . $user->last_name . '</b>: <br> <b>' . $best_bonus->amount . ' KZT </b>'  . $best_bonus->comment . '<br><br>';
                    }
                }

            }

            return $has_text ? $msg : 'No bonuses';
        } else {
            $bonus_ids = ObtainedBonus::where('date', date('Y-m-d'))
                ->where('amount', '>', 0)
                ->get()
                ->groupBy('bonus_id');

            foreach($bonus_ids as $bonus_id => $bonuses) {

                foreach($bonuses as $bonus) {
                    $user = User::withTrashed()->find($bonus->user_id);

                    if($user) {
                        $msg .= $user->name . ' ' . $user->last_name . ': ' . $bonus->amount . ' KZT '  . $bonus->comment . '<br>';
                    }

                }

            }

            return count($bonus_ids) > 0 ? $msg : 'No bonuses';
        }



    }
}
