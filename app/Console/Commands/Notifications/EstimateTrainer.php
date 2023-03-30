<?php

namespace App\Console\Commands\Notifications;

use App\ProfileGroup;
use App\ProfileGroup\ProfileGroupUsersQuery;
use App\User;
use Illuminate\Console\Command;
use App\UserNotification;
use Carbon\Carbon;

class EstimateTrainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usernotification:estimate_trainer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Уведолмение об оценке руководителя и старшего спеца. За 2 дня до конца месяца';

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
        if($this->notRightDay()) return null;
 
        $user_ids = $this->getUserIds();

        foreach($user_ids as $user_id) {

            $msg_fragment = 'Пожалуйста, оцените их по 10-бальной шкале<br>';
            $msg_fragment .=  '<a href="/estimate_your_trainer" class="btn btn-primary btn-sm rounded mt-1"  target="_blank">Оценить</a>';
            $months = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
            UserNotification::create([
                'user_id' => $user_id,
                'about_id' => 0,
                'title' => 'Оцените работу Вашего руководителя и старшего специалиста за '. $months[Carbon::now()->month-1],
                'group' => now(),
                'message' => $msg_fragment
            ]);

        }

        $this->comment('users: ' . count($user_ids));
        $this->comment('Notifications sent');

    }

    const superiorsIds = [45,55]; // Руководитель, старший специалист группы

    public function getUserIds() {
        $groupIds = ProfileGroup::where('active', 1)
            ->get()
            ->pluck('id')
            ->toArray();

        $groupIdsWithSuperiors = (new ProfileGroupUsersQuery())
            ->whereIsTrainee(false)
            ->deletedByMonthFilter(1, null)
            ->groupeFilter($groupIds, null)
            ->wherePositionIds(self::superiorsIds)
            ->getGroupIds();

        return (new ProfileGroupUsersQuery())
                ->whereIsTrainee(false)
                ->deletedByMonthFilter(1, null)
                ->groupeFilter($groupIdsWithSuperiors, null)
                ->whereNotPositionIds(self::superiorsIds)
                ->getUserIds();
    }

    public function notRightDay()
    {
        $date = Carbon::now()->endOfMonth()->subDays(1)->format('Y-m-d');
        return $date != date('Y-m-d');
    }
}
