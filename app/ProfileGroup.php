<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Books\BookGroup;
use App\User;
use Carbon\Carbon;
use Spatie\Permission\Traits\HasRoles;

class ProfileGroup extends Model
{
    use HasRoles;

    protected $table = 'profile_groups';

    protected $guard_name = 'web';
    
    public $timestamps = true;

    protected $casts = [
        'checktime_users' => 'array',
        'time_exceptions' => 'array'
    ];

    protected $fillable = [
        'name', // Название группы
        'users', // Кто состоит в группе
        'work_start', // Начало
        'work_end', // Конец рабочего времени
        'workdays', // Рабочие дни в неделе
        'editors_id', // Кто может редактировать группу
        'required', // Колво требуемых сотрудников
        'provided', // Колво предоставленных
        'head_id', // Руководители
        'bp_link', // Ссылка на Zoom обучение стажеров в Bpartnbers
        'zoom_link', // Ссылка на Zoom обучение стажеров
        'checktime', // Время до которой можно отметиться
        'checktime_users', // Поле для хранения, кто отметился. Опустошается поле конца отметки
        'salary_approved', // Начисления за предыдущий месяц утверждены
        'salary_approved_by', // Начисления за предыдущий месяц утверждены кем
        'salary_approved_date', // Когда утверждены
        'active', // Существует или нет
        'payment_terms', // Условия оплаты труда
        'has_analytics', // есть ли аналитика   0 нет 1 создана -1 архивирована
        'quality', // контроль качества с ucalls или локальный
        'editable_time', // редактируется табель или нет 1 и 0
        'time_address', // откуда брать часы
        'time_exceptions', // сотрудники-исключения, которым часы не подтягиваются
        'paid_internship', // оплачиваемая стажировка 1 0
        'rentability_max', // предел рентабельности для спидометра
        'show_payment_terms', // показывать в профиле условия оплаты труда
    ];

    // time_address
    CONST FROM_UCALLS = -1;
    CONST NOWHERE = 0;

    public function dialer()
    {
        return $this->hasOne('App\Models\CallibroDialer', 'group_id', 'id');
	}

    public function plan()
    {
        return $this->hasOne('App\GroupPlan', 'group_id', 'id');
	}
		
    public function groupUsers(){

        $user_ids = json_decode($this->users);

        if (json_last_error() === JSON_ERROR_NONE && is_array($user_ids) && count($user_ids)) {
            $user_ids = array_unique($user_ids);
            return User::selectRaw("*,CONCAT(name,' ',last_name) as full_name")->whereIn('id', $user_ids)->get();
        }

        return null;
    }

    public static function addBookgroupsToProfileGroup($profile_id, $groups) {
        $profile = ProfileGroup::find($profile_id);

        $group_ids = [];
        foreach($groups as $group) {
            array_push($group_ids, $group['id']); 
        }
        
        if($profile) {
            $profile->book_groups = json_encode($group_ids);
            $profile->save();
        }

    }

    public static function getBookGroupsArray($profile_id)
    {   
        $profile = self::find($profile_id);
        
        if($profile) {
            return json_decode($profile->book_groups) ? json_decode($profile->book_groups) : [];
        } else {
            return [];
        }

    }

    public static function getBookGroups($profile_id)
    {   
        $profile = ProfileGroup::find($profile_id);

        if($profile) {
            $book_group_ids = json_decode($profile->book_groups);
            return BookGroup::whereIn('id', $book_group_ids)->get();
        } else {
            return collect(new BookGroup);
        }
    }

    /**
     * @return array
     */
    public static function pluckIdName()
    {   
        $groups_collection = self::get();

        $groups = [
            '0' => 'Все группы', // Все
        ]; 

        foreach($groups_collection as $group) {
            $groups[$group->id] = $group->name;
        }

        return $groups;
    }

    /**
     * Получить руководителей всех групп
     */
    public static function getHeads() {
        $groups = ProfileGroup::get();

        $heads = [];

        foreach ($groups as $key => $group) {
            $g_heads = json_decode($group->head_id);
            $heads = array_merge($heads, $g_heads);
        }

        return array_unique($heads);
    }

    /**
     * Где является руководм сотрудник
     *
     */
    public static function headIn($user_id, $is_array = true) {
        $groups = self::get();

        $g = [];

        foreach ($groups as $key => $group) {
            if(in_array($user_id, json_decode($group->head_id))) {
                array_push($g, $group->id);
            }
        }

        if($is_array) {
            return array_unique($g);
        } else {
            if(count($g) > 0) {
                return self::where('id', array_unique($g))->get();
            } else {
                return collect([]);
            }
        }
        
    }

    /**
     * User in groups
     */
    public static function userIn($user_id, $is_array = true) {
        $groups = self::get();

        $g = [];

        foreach ($groups as $key => $group) {
            if($group->users == null) continue;
            if(in_array($user_id, json_decode($group->users))) {
                array_push($g, $group->id);
            }
        }

        if($is_array) {
            return array_unique($g);
        } else {
            return self::whereIn('id', array_unique($g))->get();
        }
        
    }

    public static function employees($group_id, $date = null, $user_types = 0, $positions = []) {
        $group = self::find($group_id);
        $users = [];

        if($user_types == 0 || $user_types == 1) {
            $group_users = json_decode($group->users);
            if($group->users == null) $group_users = [];
            
            $users = $group_users;
            
            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', $users)
                ->where('is_trainee', 0)
                ->orderBy('last_name', 'asc')
                ->select(['users.id','users.last_name', 'users.name']);
                
            if(count($positions) > 0) $users->whereIn('position_id', $positions);
            $users = $users->get()
                ->pluck('id')
                ->toArray();
        }  
        
        if($user_types == 0 || $user_types == 2) { 
            $fired_users = [];
            if($date) {
                $date = Carbon::parse($date);
                $x_users = \DB::table('users')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('is_trainee', 0)
                    ->whereDate('deleted_at', '>=', Carbon::createFromDate($date->year, $date->month, 1)->format('Y-m-d'));
                    
                    if(count($positions) > 0) $x_users->whereIn('position_id', $positions);
                    $x_users = $x_users->get(['users.id','users.last_group']);
            
                foreach($x_users as $d_user) {
                    if($d_user->last_group) { 
                        $lg = json_decode($d_user->last_group);
                        if(in_array($group_id, $lg)) {
                            array_push($fired_users, $d_user->id);
                        }
                    } 
                }
            }
        
            $users = $users + $fired_users;
        }
            
        return array_unique($users);

    }

    /**
     * same as employees()
     */
    public function workers($date = null) { 
      
        $users = json_decode($this->users);

        $users = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->whereIn('users.id', $users)
            ->where('is_trainee', 0)
            ->orderBy('last_name', 'asc')
            ->select(['users.id','users.last_name', 'users.name'])
            ->get()
            ->pluck('id')
            ->toArray();

            $fired_users = [];
        if($date) {
            $date = Carbon::parse($date);
            $x_users = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->whereDate('deleted_at', '>=', Carbon::createFromDate($date->year, $date->month, 1)->format('Y-m-d'))
                ->get(['users.id','users.last_group']);

          
            foreach($x_users as $d_user) {
                if($d_user->last_group) { 
                    $lg = json_decode($d_user->last_group);
                    if(in_array($this->id, $lg)) {
                        array_push($fired_users, $d_user->id);
                    }
                } 
            }
        }
      
        $users = $users + $fired_users;
        return array_unique($users);

    }

    /**
     * Temp fcuntion for count worked users on month
     */
    public static function employees2($group_id, $date, $user_types = 0, $positions = []) {
        $group = self::find($group_id);
        $users = [];

        $date = Carbon::parse($date);

        if($user_types == 0 || $user_types == 1) {
            $users = json_decode($group->users);
            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', $users)
                ->where('is_trainee', 0)
                ->whereYear('ud.applied', $date->year)
                ->whereMonth('ud.applied', $date->year)
                ->orderBy('last_name', 'asc')
                ->select(['users.id','users.last_name', 'users.name']);
                
            if(count($positions) > 0) $users->whereIn('position_id', $positions);
            $users = $users->get()
                ->pluck('id')
                ->toArray();
        }  
        
        if($user_types == 0 || $user_types == 2) { 
            $fired_users = [];
            if($date) {
                $date = Carbon::parse($date);
                $x_users = \DB::table('users')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('is_trainee', 0)
                    ->whereDate('deleted_at', '>=', Carbon::createFromDate($date->year, $date->month, 1)->format('Y-m-d'));
                    
                    if(count($positions) > 0) $x_users->whereIn('position_id', $positions);
                    $x_users = $x_users->get(['users.id','users.last_group']);
            
                foreach($x_users as $d_user) {
                    if($d_user->last_group) { 
                        $lg = json_decode($d_user->last_group);
                        if(in_array($group_id, $lg)) {
                            array_push($fired_users, $d_user->id);
                        }
                    } 
                }
            }
        
            $users = $users + $fired_users;
        }
            
        return array_unique($users);

    }

    public function trainees($date = null) 
    {
        $users = json_decode($this->users);

        $users = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->whereIn('users.id', $users)
            ->where('is_trainee', 1)
            ->orderBy('last_name', 'asc')
            ->select(['users.id','users.last_name', 'users.name'])
            ->get()
            ->pluck('id')
            ->toArray();

            $fired_users = [];
        if($date) {
            $date = Carbon::parse($date);
            $x_users = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 1)
                ->whereDate('deleted_at', '>=', Carbon::createFromDate($date->year, $date->month, 1)->format('Y-m-d'))
                ->get(['users.id','users.last_group']);


          
            foreach($x_users as $d_user) {
                if($d_user->last_group) { 
                    $lg = json_decode($d_user->last_group);
                    if(in_array($this->id, $lg)) {
                        array_push($fired_users, $d_user->id);
                    }
                } 
            }
        }
      
        $users = $users + $fired_users;
        return array_unique($users);
    }
}
