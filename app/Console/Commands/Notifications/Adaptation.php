<?php

namespace App\Console\Commands\Notifications;

use App\ProfileGroup;
use App\User;
use App\Models\Bitrix\Lead;
use Illuminate\Console\Command;
use App\DayType;
use App\Classes\Helpers\Phone;
use App\UserNotification;
use App\Salary;
use Carbon\Carbon;
use App\Models\User\NotificationTemplate;

class Adaptation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usernotification:adaptation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Уведолмение о заполнении таблицы адаптации';

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
        if(date('w') == '6' || date('w') == '0') return '';

        $date = Carbon::now()->subDays(70)->format('Y-m-d');
        $leads = Lead::query()->where('invite_at', '>', $date)->get();


        foreach($leads as $lead) {

            $user = User::with('user_description')
                ->withTrashed()
                ->whereHas('user_description', function ($query) {
                    $query->where('is_trainee', 0);
                })
                ->where('users.id', $lead->user_id)
                ->first();

            if(!$user) continue;

            //$start_day = Carbon::parse($lead->invite_at)->startOfDay();
            $start_day = Carbon::parse($user->applied_at())->startOfDay();
            $send_day = $this->getDayToSend($start_day->format('Y-m-d'), $user->id);

            if(in_array($send_day, [4,15,30,45])) { // Отправляем в эти дни

                $groups = $user->groups()->where('status', 'active')->get();

                if($groups->count() == 0) continue;

                $msg_fragment =  $send_day . ' день <br>';
                $msg_fragment .=  '<a href="https://'.tenant('id').'.jobtron.org/timetracking/edit-person?id=' . $user->id . '">' . $user->last_name . ' ' . $user->name . '</a><br>';
                $msg_fragment .=  $groups[0]->name;

                $timestamp = now(); 
                $notification_receivers = NotificationTemplate::getReceivers(8, $groups[0]->id); // Уведолмение Заполнита таблицу адаптации
                
                foreach($notification_receivers as $user_id) {
                    UserNotification::create([
                        'user_id' => $user_id,
                        'about_id' => $lead->user_id,
                        'title' => 'Заполните таблицу адаптации',
                        'group' => $timestamp,
                        'message' => $msg_fragment
                    ]);

                }

            }

            
        }


        
    }

    /**
	 * Get send time
	 * @return int  
	 */
	public function getDayToSend($date, $user_id) {
		$start = Carbon::parse($date)->startOfDay();
		$now = Carbon::now()->startOfDay();

		$diff = $now->timestamp - $start->timestamp;

		if($diff <= 0) { 
			return 0;
		}

		$days = $start->diffInDaysFiltered(function (Carbon $date) {
			return $date->isWeekday();
		}, $now); 

        $first_day_was_absent = DayType::where('date', $date)->where('user_id', $user_id)->where('type', 2)->first();
		return $first_day_was_absent ? $days : $days + 1;
	}
}