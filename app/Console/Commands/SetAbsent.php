<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use App\DayType;
use App\ProfileGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\TimetrackingHistory;
use App\Timeboard\UserPresence;
use App\Models\User\NotificationTemplate;
use App\UserNotification;
use App\Models\Bitrix\Lead;
use App\External\Bitrix\Bitrix;

class SetAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отметить отсутстовавших в табели после истечения 30 минутной ссылки для стажеров';

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
        $groups = ProfileGroup::where('active', 1)->get();
        $marked_users = UserPresence::where('date', date('Y-m-d'))->get()->pluck('user_id')->toArray();
        
        foreach($groups as $group) {
              
                if($group->checktime && Carbon::parse($group->checktime)->timestamp - time() < 0) { // если время отметок истекло
                
                    $users = \DB::table('users')
                        ->whereNull('deleted_at')
                        ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                        ->where('ud.is_trainee', 1)
                        ->whereIn('users.id', json_decode($group->users))
                        ->get();

                    foreach($users as $user) {
                        // Check for trainee invited day is passed or today
                        $lead = Lead::where('user_id', $user->id)->first();
                        if($lead) {
                            
                            if($lead->invite_at) {
                                $invited_at = Carbon::parse($lead->invite_at)->subHours(6)->timestamp;
                                $now = Carbon::now()->setTimezone('Asia/Dacca')->timestamp;
                                $diff = $now - $invited_at;
                                if($diff < 0) { // not come invited day
                                    $this->line($user->id . '  not come invited day');
                                    continue;
                                }
                            } else {
                                $this->line($user->id . ' not invited');
                                continue;
                            } 
                        }

                        // if not marked
                        if(!in_array($user->id, $marked_users)) {
                     
                            $daytype = DayType::where([
                                'user_id' => $user->id,
                                'date' => Carbon::now()->format('Y-m-d'),
                            ])->first();

                            if($daytype) {
                                $daytype->update([
                                    'type' => 2,
                                    'email' => $user->email,
                                    'admin_id' => 1,
                                ]);
                            } else {
                                $daytype = DayType::create([
                                    'user_id' => $user->id,
                                    'type' => 2,
                                    'email' => $user->email,
                                    'date' => Carbon::now()->format('Y-m-d'),
                                    'admin_id' => 1,
                                ]);
                            }

                            $th = TimetrackingHistory::where([
                                'user_id' => $user->id,
                                'author' => 'Система',
                                'date' => Carbon::now()->format('Y-m-d'),
                                'description' => 'Не отметился по указанной ссылке для стажеров',
                            ])->first();

                            if(!$th) {
                                $th = TimetrackingHistory::create([
                                    'user_id' => $user->id,
                                    'author_id' => 5,
                                    'author' => 'Система',
                                    'date' => Carbon::now()->format('Y-m-d'),
                                    'description' => 'Не отметился по указанной ссылке для стажеров',
                                ]);
                            }
                            
                            $this->line($user->id . ' NOT marked');
                            $this->notify($user, $group->name);
                        }   else {

                            /** Проверить daytype на отсутствие */
                            $daytype = DayType::where([
                                'user_id' => $user->id,
                                'date' => date('Y-m-d'),
                            ])->first();
                            
                            /** DANGER  ODD function */
                            $notifications = UserNotification::where('about_id', $user->id)
                                ->where('title', 'like', 'Пропал с обучения%')
                                ->whereDate('group', date('Y-m-d'))
                                ->delete();
                            
                            // on tabel history
                            $th = TimetrackingHistory::where([
                                'user_id' => $user->id,
                                'author' => 'Система',
                                'date' => date('Y-m-d'),
                                'description' => 'Не отметился по указанной ссылке для стажеров',
                            ])->delete();

                            /////
                            if($daytype) $daytype->update([
                                'type' => 5,
                            ]);

                            $this->line($user->id . ' marked');
                        }

                        
                    }

                    $group->checktime_users = [];
                    $group->checktime = null;
                    $group->save();
                }
            
        }
    }

    public function notify($targetUser, $group_name) {
     
        $group_name = '(' . $group_name . ')';
        $editPersonLink = 'https://bp.jobtron.org/timetracking/edit-person?id=' . $targetUser->id;

        $abs_msg = 'Система: '. $group_name .'  Стажер не был на обучении: <br> <a href="' . $editPersonLink . '" target="_blank">';
        $abs_msg .= $targetUser->last_name . ' ' . $targetUser->name  . ' </a>';
        $abs_msg .= '<br><a href="/timetracking/analytics/skypes/' . $targetUser->lead_id . '" target="_blank" class="btn btn-primary mr-2 mt-2 rounded btn-sm">Перейти в сделку</a>';
        $abs_msg .= '<a class="btn btn-primary mt-2 rounded btn-sm transfer-training" data-userid="' . $targetUser->id . '">Перенести обучение</a>';

        $timestamp = Carbon::now(); 

        $notification_temp_id = 2;
        $title_lost = 'Пропал с обучения';
        $notification_receivers = NotificationTemplate::getReceivers($notification_temp_id);
        
        /////
        $lead = Lead::where('user_id', $targetUser->id)->first();

        if($lead) {
            if(Carbon::parse($lead->invite_at)->day == date('d')) {
                $title_lost = 'Пропал с обучения: 1 день';
                $notification_temp_id = 4;
            } else if(Carbon::parse($lead->day_second)->day == date('d')) {
                $title_lost = 'Пропал с обучения: 2 день';
                $notification_temp_id = 5;
            }

            ///////// // перенос сделки с Обучается на Пропал с обучения в БИТРИКС

            $bitrix = new Bitrix();

            $deal_id = 0;
            if($lead->deal_id != 0) {
                $deal_id = $lead->deal_id;
            } 
            // else if($lead->lead_id != 0) {
            //     $deal_id = $bitrix->findDeal($lead->lead_id, false);
            //     usleep(1000000); // 1 sec
            // } 
            
            if($deal_id != 0) {
                $bitrix->changeDeal($deal_id, [
                    'STAGE_ID' => 'C4:21' 
                ]);
                usleep(1000000); // 1 sec
            }
        }

       

        //////

        
        foreach($notification_receivers as $user_id) {

            $un =  UserNotification::where([
                'user_id' => $user_id,
                'about_id' => $targetUser->id,
                'title' => $title_lost
            ])
            ->whereDate('group', $timestamp->format('Y-m-d'))
            ->first();
            
            if(!$un) {
                UserNotification::create([
                    'user_id' => $user_id,
                    'about_id' => $targetUser->id,
                    'title' => $title_lost,
                    'message' => $abs_msg,
                    'group' => $timestamp
                ]);
            }
           
            
            
        }
    }
}
