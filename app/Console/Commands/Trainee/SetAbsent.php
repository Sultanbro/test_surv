<?php

namespace App\Console\Commands\Trainee;

use App\Service\Department\UserService;
use App\User;
use Carbon\Carbon;
use App\DayType;
use App\ProfileGroup;
use Illuminate\Console\Command;
use App\TimetrackingHistory;
use App\Models\Timetrack\UserPresence;
use App\Models\User\NotificationTemplate;
use App\UserNotification;
use App\Models\Bitrix\Lead;
use App\Api\BitrixOld as Bitrix;

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
     * marked trainees
     */
    protected $marked_users;

    /**
     * departments
     */
    protected $groups;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
//
//        $this->groups = ProfileGroup::where('active', 1)->get();
//
//        $this->marked_users = UserPresence::where('date', date('Y-m-d'))
//            ->get()->pluck('user_id')->toArray();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach($this->groups as $group) {

            if($this->isTimeToMarkHasExpired($group)) { 
                continue;
            }

            $users = collect(
                (new UserService)->getTrainees($group->id, date('Y-m-d'))
            );

            foreach($users as $user) {

                if($this->notComeInviteDay($user)) {
                    continue;
                }

                $traineeMarked = in_array($user->id, $this->marked_users);
                $traineeNotMarked = !$traineeMarked;

                if($traineeNotMarked) {
                    
                    $this->mark($user, DayType::DAY_TYPES['ABCENSE']);

                    // $this->logHistory($user);
                    
                    $this->notify($user, $group->name);

                    $this->line($user->id . ' - has marked');
                } 

                if($traineeMarked) {

                    $this->mark($user, DayType::DAY_TYPES['TRAINEE']);
                    
                    $this->deleteOldInfo($user);

                    $this->line($user->id . ' - was marked before');
                }

            }

            $this->resetCheckTime($group);
            
        }
    }

    /**
     * Сбросить время отметок
     * 
     * @param ProfileGroup $group
     * @return void
     */
    protected function resetCheckTime(ProfileGroup $group) 
    {
        $group->checktime_users = [];
        $group->checktime = null;
        $group->save();
    }

    /**
     * Удалить утратившие силу данные
     * 
     * @param User $user
     * @return void
     */
    protected function deleteOldInfo(User $user) 
    {
        // Delete notifications about Absense of user
        UserNotification::where('about_id', $user->id)
            ->where('title', 'like', 'Пропал с обучения%')
            ->whereDate('group', date('Y-m-d'))
            ->delete();
        
        // delete log
        $th = TimetrackingHistory::where([
            'user_id' => $user->id,
            'author' => 'Система',
            'date' => date('Y-m-d'),
            'description' => 'Не отметился по указанной ссылке для стажеров',
        ])->delete();
    }

    /**
     * Записать в историю
     * 
     * @param User $user
     * @return void
     */
    protected function logHistory(User $user) 
    {
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
    }

    /**
     * Отметить день как стажировка
     * 
     * @param User $user
     * @param int $daytype
     * @return void
     */
    protected function mark(User $user, int $type) 
    {
        $daytype = DayType::where([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ])->first();

        if($daytype) {
            $daytype->update([
                'type' => $type,
                'email' => $user->email,
                'admin_id' => 1,
            ]);
        } else {
            $daytype = DayType::create([
                'user_id' => $user->id,
                'type' => $type,
                'email' => $user->email,
                'date' => Carbon::now()->format('Y-m-d'),
                'admin_id' => 1,
            ]);
        }
    }

    /**
     * Не пришел день стажировки соискателя
     * 
     * @param User $user
     * @return bool
     */
    protected function notComeInviteDay(User $user) 
    {
        $lead = Lead::where('user_id', $user->id)->first();

        if( !$lead ) return false;

        if( !$lead->invite_at ) {
            $this->line($user->id . ' not invited');
            return true;
        }

        $invited_at = Carbon::parse($lead->invite_at)->subHours(6)->timestamp;
        $now = Carbon::now()->setTimezone('Asia/Dacca')->timestamp;
        
        if($now - $invited_at < 0) { 
            $this->line($user->id . '  not come invited day');
            return true;
        }

        return false;
    }

    /**
     * Время отметок стажеров по временной ссылке истекло 
     * 
     * @param ProfileGroup $group
     * @return bool
     */
    protected function isTimeToMarkHasExpired(ProfileGroup $group) 
    {
        return $group->checktime
            && Carbon::parse($group->checktime)->timestamp - time() < 0;
    }

    /**
     * Form notification about User absence 
     * Change stage of Bitrix Deal to absent
     * Notify head recruiters in jobtron
     * 
     * @return void
     */
    protected function notify($targetUser, $group_name)
    {
        
        $lead = Lead::where('user_id', $targetUser->id)->first();

        $group_name = '(' . $group_name . ')';
        $editPersonLink = 'https://'.tenant('id').'.jobtron.org/timetracking/edit-person?id=' . $targetUser->id;

        $abs_msg = 'Система: '. $group_name .'  Стажер не был на обучении: <br> <a href="' . $editPersonLink . '" target="_blank">';
        $abs_msg .= $targetUser->last_name . ' ' . $targetUser->name  . ' </a>';

        if($lead) {
            $abs_msg .= '<br><a href="/timetracking/analytics/skypes/' . $lead->lead_id . '" target="_blank" class="btn btn-primary mr-2 mt-2 rounded btn-sm">Перейти в сделку</a>';
            $abs_msg .= '<a class="btn btn-primary mt-2 rounded btn-sm transfer-training" data-userid="' . $targetUser->id . '">Перенести обучение</a>';
        } else {
            $abs_msg .= '<br><a class="btn btn-primary mt-2 rounded btn-sm transfer-training" data-userid="' . $targetUser->id . '">Перенести обучение</a>';
        }
        
      

        $timestamp = Carbon::now(); 

        $notification_temp_id = 2;
        $title_lost = 'Пропал с обучения';
        $notification_receivers = NotificationTemplate::getReceivers($notification_temp_id);
        
        // Change stage of Bitrix Deal
        if($lead) {

            if(Carbon::parse($lead->invite_at)->day == date('d')) {
                $title_lost = 'Пропал с обучения: 1 день';
                $notification_temp_id = 4;
            } else if(Carbon::parse($lead->day_second)->day == date('d')) {
                $title_lost = 'Пропал с обучения: 2 день';
                $notification_temp_id = 5;
            }

            // перенос сделки с Обучается на Пропал с обучения в БИТРИКС
            $deal_id = $lead->deal_id != 0
                ? $lead->deal_id
                : 0;

            if($deal_id != 0) {
                (new Bitrix)->changeDeal($deal_id, [
                    'STAGE_ID' => 'C4:21' 
                ]);
                usleep(1000000); // 1 sec
            }
        }

       

        // Notification in Jobtron
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
