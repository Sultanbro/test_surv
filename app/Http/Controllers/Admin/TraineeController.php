<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use App\ProfileGroup;
use App\User;
use App\UserNotification;
use App\TimetrackingHistory;
use App\Trainee;
use App\DayType;
use App\Models\Books\BookGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\External\Bitrix\Bitrix;
use App\Models\Bitrix\Lead;
use App\AnalyticsSettingsIndividually;
use App\Timeboard\UserPresence;

class TraineeController extends Controller
{
    public function __construct()
    {

    }

    public function autochecker() {
        $this->middleware('auth');
        $user = Auth::user(); 

        $groups = $user->headInGroups();

        foreach($groups as $group) {
            if(Carbon::parse($group->checktime)->timestamp - time() >= 0) {
                $group->checktime = Carbon::parse($group->checktime)->setTimezone('Asia/Almaty');
            } else {
                $group->checktime = null;
            }
            
        }

        return view('admin.autocheck.autochecker')->with([
            'groups' => $groups
        ]);  
    }

    public function open($id, Request $request) {
        $this->middleware('auth');
        $user = Auth::user(); 

        if ($request->isMethod('post')) {
            $group = ProfileGroup::find($id);
            if($group && Carbon::parse($group->checktime)->timestamp - time() <= 0) {
                $group->checktime = Carbon::now()->addMinutes(30);
                $group->save();
            }
        } 
        
        // $groups = $user->headInGroups();

        // foreach($groups as $group) {
        //     if(Carbon::parse($group->checktime)->timestamp - time() >= 0) {
        //         $group->checktime = Carbon::parse($group->checktime)->setTimezone('Asia/Almaty');
        //     } else {
        //         $group->checktime = null;
        //     }
            
        // }

        return [
            'code' => 200,
            'time' => Carbon::now()->addMinutes(30)->addHours(6)->format('H:i')
        ];
        // return view('admin.autocheck.autochecker')->with([
        //     'groups' => $groups
        // ]);  
    }

    public function autocheck($id) {
        
        
        $group = ProfileGroup::find($id);
        if(!$group) abort(404);
        
        $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 1)
            ->where('UF_ADMIN', 1) 
            ->whereIn('users.id', json_decode($group->users, true))
            ->select(DB::raw("CONCAT_WS(' ', last_name, name) as name"), 'users.ID as id')->get()->sortBy('name')->toArray();
       // $users = User::where('UF_ADMIN', 1)->select(DB::raw("CONCAT_WS(' ', last_name, name) as name"), 'ID as id')->get()->sortBy('name')->toArray();

        foreach($users as $key => $user) {
            $users[$key]['id'] = $this->numhash($user['id']);
        }

		return view('admin.autocheck.index')->with([
            'users' => $users
        ]);
	}

    /**
     * Trainee marked
     */
    public function save($id, Request $request) {
        //dd($request->ip());

        $group = ProfileGroup::find($id);

        $user_id = $this->numhash($request->user_id);

        $user = User::find($user_id);
        $user = $user ? $user->last_name . ' ' . $user->name : '';

        if($group && $group->checktime && Carbon::parse($group->checktime)->timestamp - time() >= 0) {
            // $users = $group->checktime_users;
            // array_push($users, $user_id);
            // $users = array_unique($users);
            // $group->checktime_users = $users;
            // $group->save();

            /** Отметиться */
            $marked_user = UserPresence::where('date', date('Y-m-d'))
                ->where('user_id', $user_id)
                ->first();
            if(!$marked_user) {
                UserPresence::create(['user_id' => $user_id, 'date' => date('Y-m-d')]);
            }

            /** Проверить daytype на отсутствие */
            $daytype = DayType::where([
                'user_id' => $user_id,
                'date' => date('Y-m-d'),
            ])->first();
            
            /** DANGER  ODD function */
            $notifications = UserNotification::where('about_id',$user_id)
                ->where('title', 'like', 'Пропал с обучения%')
                ->whereDate('group', date('Y-m-d'))
                ->delete();
            

            // on tabel history
            $th = TimetrackingHistory::where([
                'user_id' => $user_id,
                'author' => 'Система',
                'date' => date('Y-m-d'),
                'description' => 'Не отметился по указанной ссылке для стажеров',
            ])->delete();

            /////
            if($daytype) $daytype->update([
                            'type' => 5,
                        ]);

            // сообщение 
            $message = 'Вы успешно отметились! Можете возвращаться на стажировку';
        } else {
            $message = $group ? 'Не получилось отметиться. Видимо ссылка уже устарела...'  : 'Ошибка системы. Группа не найдена';
        }

        // 
		return view('admin.autocheck.save')->with([
            'message' =>  $message,
            'user' =>  $user,
        ]);
	}

    private function numhash($n) {
        return (((0x0000FFFF & $n) << 16) + ((0xFFFF0000 & $n) >> 16));
    }
    
  
}
