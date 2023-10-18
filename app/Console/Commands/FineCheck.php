<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Fine;
use App\UserFine;
use App\UserDescription;
use App\DayType;
use App\Timetracking;
use App\ProfileGroup;
use DB;
use Carbon\Carbon;

class FineCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fine:check';

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
        
        $fine_id = 53; // штраф за невыход на работу
        $notification_template = DB::table('notification_templates')
		    ->find(1); // шаблон штрафа 

        if($notification_template) {

            $groupsArray = explode(",", $notification_template->ids); // группы string to array

            $groups = ProfileGroup::whereIn('id', $groupsArray)->get(); // группы в шаблоне 

            $usersArray = []; // Соберем ID всех user в массив

            foreach($groups as $group) {
                $users = substr($group->users, 0, -1);
                $users = ltrim($users, '[');
                $users = explode(",", $users);
                $usersArray = array_merge($usersArray,$users);
            }  

            $usersArray = array_unique($usersArray); // Готовый массив с уникальными значениями


            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee',0)
                ->whereIn('users.id', $usersArray)
                ->get(); // Берем данные о полователях



            $days = ['Sunday', 'Monday'];
            foreach($days as $searchDay) {
                
                $lastDay = Carbon::createFromTimeStamp(strtotime("last $searchDay"));
                
 
                
                foreach($users as $user) {
                    $this->line($user->id);
                    $user_desc = UserDescription::where('is_trainee', 0)->where('user_id', $user->id)->first();
                    
                    if(!$user_desc) {
                        continue;
                    }
                   
                    $daytype = DayType::where('user_id', $user->id)->whereDate('date', $lastDay->format('Y-m-d'))->first();    // Если он заболел или выходной
                    if($daytype && in_array($daytype->type, [1,3])) {
                        continue;
                    }
                  
                    $tt = Timetracking::whereDay('enter', $lastDay->day)
                        ->whereMonth('enter', $lastDay->month)
                        ->whereYear('enter', $lastDay->year)
                        ->where('user_id', '=',  $user->id)
                        ->first();   
                        
                    
                    if(!$tt) {
                        $wasFine = UserFine::whereDay('day', $lastDay->day)
                            ->whereMonth('day', $lastDay->month)
                            ->whereYear('day', $lastDay->year)
                            ->where('user_id', '=',  $user->id)
                            ->where('fine_id','=',  $fine_id)
                            ->first();

                        if(!$wasFine) {
                            $userFine = new UserFine;
                            $userFine->user_id = $user->id;
                            $userFine->fine_id = $fine_id;
                            $userFine->day = $lastDay;
                            $userFine->save();

                            $title = UserFine::getTemplate('2500', Carbon::parse($lastDay)->format('d.m.Y')); 
                            $data = [];

                            $data['date'] = Carbon::parse($lastDay)->format('Y-m-d');
                            UserFine::setNotificationAboutFine($user->id, $fine_id, $title, $data);
                        }
                    }
                }
            
            }

        }
    }
}
