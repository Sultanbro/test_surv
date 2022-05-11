<?php

namespace App\Console\Commands\Notifications;

use App\ProfileGroup;
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

            UserNotification::create([
                'user_id' => $user_id,
                'about_id' => 0,
                'title' => 'Оцените работу Вашего руководителя и старшего специалиста за "месяц, за который просим оценить"',
                'group' => now(),
                'message' => $msg_fragment
            ]);

        }

        $this->comment('users: ' . count($user_ids));
        $this->comment('Notifications sent');

    }

    /**
	 * Есть руководы
	 * @return int  
	 */
	public function hasSuperiors($group)
    {
        $user_ids = ProfileGroup::employees($group->id);
        $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'b_user.ID')
            ->where('UF_ADMIN', 1)
            ->where('is_trainee', 0)
            ->whereIn('b_user.ID', $user_ids) 
            ->whereIn('position_id', [45,55]) // Руководитель, старший специалист группы
            ->get(['b_user.ID'])
            ->count();

		return $users > 0;
	}


    public function getUserIds() {
        $groups = ProfileGroup::where('active', 1)->get();
        $user_ids = [];
      
        foreach($groups as $group) {
            if($this->hasSuperiors($group)) {
                $gr_users = ProfileGroup::employees($group->id);
                $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'b_user.ID')
                    ->where('UF_ADMIN', 1)
                    ->where('is_trainee', 0)
                    ->whereIn('b_user.ID', $gr_users)
                    ->whereNotIn('position_id', [45,55]) // Руководитель, старший специалист группы
                    ->get(['b_user.ID'])
                    ->pluck('ID')
                    ->toArray();

                $user_ids = array_merge($user_ids, $users);  
            }
        }

        return array_unique($user_ids);
    }

    public function notRightDay()
    {
        $date = Carbon::now()->endOfMonth()->subDays(1)->format('Y-m-d');
        return $date != date('Y-m-d');
    }
}