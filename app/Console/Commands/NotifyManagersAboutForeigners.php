<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\User;
use App\Models\Bitrix\Lead;
use Illuminate\Console\Command;
use App\Http\Controllers\IntellectController as IC;
use App\DayType;
use App\Classes\Helpers\Phone;
use App\UserNotification;
use App\Salary;
use Carbon\Carbon;
use App\Models\User\NotificationTemplate;

class NotifyManagersAboutForeigners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usernotification:foreigner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Уведомление руководителей групп об оплате иностранным стажерам. Запускается ТОЛЬКО в понедельник.';

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
        if(date('w') != '1') return ''; 

        $from = Carbon::now()->subDays(7)->format('Y-m-d'); // с Предыдущего понедельника
		$to = Carbon::now()->subDays(3)->format('Y-m-d'); // по Предыдущую пятницу

        $users = \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->get();
      
        foreach($users as $user) {

            // $trainee = Trainee::whereNull('applied')->where('user_id', $user->id)->first();
 
            // if($trainee) {
            //     continue;
            // }

            $phone = Phone::normalize($user->phone ?? '0');
            $code = substr($phone,0,2);
          
            if($phone && strlen($phone) >= 11 && !in_array($code, ['77', '87'])) {
                $groups = $user->inGroups();
                $msg_fragment = $user->phone . ' : ' .$user->name . ' ' . $user->last_name . '<br>';

                if($groups) {

                    $heads = json_decode($groups[0]->head_id);

                    if(count($heads) > 0) {
                        $salaries = Salary::whereBetween('date', [$from, $to])->where('user_id', $user->id)->get()->sum('amount');
                        
                        $from = Carbon::parse($from)->format('d.m.Y');
                        $to = Carbon::parse($to)->format('d.m.Y');

                        $msg_fragment .= 'Период: ' . $from . ' - ' . $to . '<br>';
                        $msg_fragment .= 'Сумма: ' . $salaries . ' тг<br>';
                        $msg_fragment .= '<a href="https://'.tenant('id').'.jobtron.org/timetracking/salaries">Страница начислений</a>';

                        $timestamp = now();
                        
                        $notification_receivers = NotificationTemplate::getHeadReceivers(3, $groups[0]->id);
                
                        foreach($notification_receivers as $user_id) {
                            UserNotification::create([
                                'user_id' => $user_id,
                                'about_id' => $user->id,
                                'title' => 'Оплатите начисления иностранному сотруднику',
                                'group' => $timestamp,
                                'message' => $msg_fragment
                            ]);

                        }
                    }
                    
                } else {
                    continue;
                }



            }   
        }

       
        // dump($msg);
        // dump(' END OF SENDING');

        
    }
}