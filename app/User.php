<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\OauthClientToken as Oauth;
use App\External\Bitrix\Bitrix;
use App\Exam;
use App\DayType;
use App\UserDescription;
use App\TimetrackingHistory;
use App\Http\Controllers\IntellectController as IC;
use App\Classes\Helpers\Phone;
use App\ProfileGroupUser as PGU;
use App\Models\CourseResult;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\Access\Authorizable;

class User extends Authenticatable implements Authorizable
{
    use Notifiable,HasFactory,HasRoles,SoftDeletes;

    const USER_TYPE_OFFICE = 'office';
    const USER_TYPE_REMOTE = 'remote';

    protected $table = 'users';

    public $timestamps = true;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'password',
        'remember_token',
        'position_id',
        'program_id',
        'full_time',
        'user_type',
        'city',
        'address',
        'description',
        'currency',
        'timezone',
        'segment',
        'working_day_id',
        'working_time_id',
        'working_country',
        'working_city',
        'work_start',
        'work_end',
        'birthday', // admin.u-marketing
        'last_group',
        'read_corp_book_at',
        'has_noti',
        'notified_at',
        'role_id',
        'is_admin',
        'groups_all',
        'applied_at', // дата принятия
        'weekdays', // 0000000
        'img_url',/// найменование аватарки
        'headphones_sum',/// сумма наушника
        'phone_1',
        'phone_2',
        'phone_3',
        'phone_4',
    ];

    public function groupKpis()
    {
        return $this->hasManyThrough(ProfileGroup::class, Kpi::class);
    }

    public function bonuses(): MorphMany
    {
        return $this->morphMany('App\Models\Kpi\Bonus', 'targetable', 'targetable_type', 'targetable_id');
    }

    public function qpremium(): MorphMany
    {
        return $this->morphMany('App\Models\QuartalPremium', 'targetable', 'targetable_type', 'targetable_id');
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany('App\ProfileGroup', 'group_user', 'user_id', 'group_id')
            ->withPivot(['created_at', 'updated_at', 'deleted_at'])->withTimestamps();
    }

    public function scopeGetDeletedFromGroupUser($query, $date)
    {
        $this->groups()->get();
    }

    /**
     * @return MorphMany
     */
    public function histories(): MorphMany
    {
        return $this->morphMany('App\Models\History', 'reference', 'reference_table', 'reference_id', 'id');
    }

    /**
     * @return MorphMany
     */
    public function kpis(): MorphMany
    {
        return $this->morphMany('App\Models\Kpi\Kpi', 'targetable', 'targetable_type');
    }

    /**
     * @return HasMany
     */
    public function statistics(): HasMany
    {
        return $this->hasMany('App\Models\Analytics\UserStat', 'user_id');
    }

    public function getCheckList()
    {
        return $this->hasMany('App\Models\CheckUsers', 'check_users_id', 'id');
    }

//    public function getCheckList()
//    {
//        return $this->hasMany('App\Models\CheckUsers', 'check_users_id', 'id');
//    }

    public function position()
    {
        return $this->belongsTo('App\Position', 'position_id');
    }

    /**
     * Получает пользователя из системных таблицы Битрикса
     */
    public static function bitrixUser()
    {
        return Auth::user();
    }

    /**
     * Дни до индексации зарплаты по должности
     */
    public function days_before_indexation() {
        $days = $this->worked_days();

        if($days == 0) {
            $remain = 999;
        } else {
            $x = floor($days / 90);
            $remain = 90 - ($days - (90 * $x));
        }

        return $remain;
    }

    /**
     * Работает у нас уже дней
     */
    public function worked_days() {
        $ud = UserDescription::where('user_id', $this->id)->first();
        if(!$ud) return 0;
        if($ud && $ud->is_trainee == 1) return 0;

        $date = Carbon::parse($this->applied_at())->timestamp;
        $now = time();

        $diff = ($now - $date) / 86400;
        return (int)$diff;

    }

    /**
     * Работал у нас дней
     */
    public function wasPartOfTeam($check_ud = false) {
        if($check_ud) {
            $ud = UserDescription::where('user_id', $this->id)->first();
            if(!$ud) return 0;
            if($ud && $ud->is_trainee == 1) return 0;
        }   
        
        $date = Carbon::parse($this->applied_at())->timestamp;
        $fired = Carbon::parse($this->deleted_at)->timestamp;

        $diff = ($fired - $date) / 86400;
        return (int)$diff;
    }

    /**
     * Рабочие дни со дня принятия
     */
    public function workdays_from_applied($date, $workdays = 6) {
        $date = Carbon::parse($date);
        $applied_from = 0;
        if($this->user_description && $this->user_description->applied) {
            $applied = Carbon::parse($this->user_description->applied);
            $applied->addDay();
            $year = $applied->year;
            $month = $applied->month;
            
            if($year == $date->year && $month == $date->month) {
                $exclude = $workdays == 5 ? 2 : 1;
                $applied_from = workdays_diff($applied->format('Y-m-d'), Carbon::parse($date)->endOfMonth()->format('Y-m-d'), $exclude);
                //$applied_from = $applied_from - 1;
                $applied_from = $applied_from < 0 ? 0 : $applied_from;
            }
        }

        return $applied_from;
    }

    /**
     * @return HasOne
     */
    public function description(): HasOne
    {
       return $this->hasOne('App\UserDescription', 'user_id', 'id');
    } 

    public function user_description()
    {
       return $this->hasOne('App\UserDescription', 'user_id', 'id');
    } 

    public function lead()
    {
       return $this->hasOne('App\Models\Bitrix\Lead', 'user_id', 'id');
    } 

    public function integration_token(String $server)
    {
        return Oauth::get_token($this->id, $server);
    }


    public static function user($user_id)
    {
        $user = User::find($user_id);
        return $user;
    }

    /**
     * Обноляет баланс после транзакции
     * @param $amount
     * @return float
     */
    public static function updateBalance($user_id, $amount)
    {
        if ($user_id > 0) {
            $user_balance = self::balanceByUser($user_id);
            $user_balance -= $amount;

            User::where('id', $user_id)
                ->update(['UF_BALANCE' => $user_balance]);
            //DB::update('UPDATE b_uts_user SET UF_BALANCE = ? WHERE VALUE_ID = ?', [$user_balance, $user_id]);
        }

        return self::balanceByUser($user_id);
    }

    /**
     * Oklad na chas
     */
    public function hourly_pay($date) {
        $zarplata = $this->zarplata ? $this->zarplata->zarplata : 70000;
        $working_hours = $this->workingTime ? $this->workingTime->time : 9;
        
        // Какие дни не учитывать в месяце
        $ignore = $this->working_day_id == 1 ? [6,0] : [0];

        $date = Carbon::parse($date);
        $workdays = workdays($date->year, $date->month, $ignore);


 

        // проверка сданных экзаменов  
        $wage = $zarplata; // WAGE: оклад + бонус от экзамена
        $bonusFromExam = 0; // бонус от экзамена
        $exam = Exam::where('user_id', $this->id) // Проверка сдавал ли сотрудник книгу в этом месяце
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->first();
    
        if(!is_null($exam) && $exam->success == 1) {
            $bonusFromExam = 10000;
            $wage += $bonusFromExam;
        }    




        return $wage / $workdays / $working_hours;
    }

    /**
     * В каких группах находится user 
     * @return array
     */
    public function inGroups()
    {
        $_groups = [];

        $groups = ProfileGroup::where('active', 1)->get();

        foreach($groups as $group) {
            if($group->users == null) {
                $group->users = '[]';
            }
            $group_users = json_decode($group->users);
            
            if(in_array($this->id, $group_users)) {
                $group->show = false;
                array_push($_groups, $group);  
            }
        }
        
        return $_groups;
    }

    /**
     * В каких группах руководит user 
     * @return array
     */
    public function headInGroups()
    {
        $_groups = [];

        $groups = ProfileGroup::where('active', 1)->get();

        foreach($groups as $group) {
            $group_users = json_decode($group->head_id);
            
            if(in_array($this->id, $group_users)) {
                array_push($_groups, $group);  
            }
        }

        return $_groups;
    }
    
    public static function deleteUser(Request $request) // Уволить сотрудника
    {   
        $user_id =  $request->user_id ? $request->user_id : $request->id;
        $user = self::find($user_id);
        
        if($user == null)  {
            return back()->withErrors('Пользователь не найден');
        }

        $_groups = [];

        $groups = ProfileGroup::where('active', 1)->get();

        foreach($groups as $group) {
            if($group->users == null) {
                $group->users = '[]';
            }
            $group_users = json_decode($group->users);
            
            if(in_array($user_id, $group_users)) {
                $group->show = false;
                array_push($_groups, $group->id);  
            }
        }
        
        foreach($_groups as $group_id) {
            $pgu = PGU::where('group_id', $group_id)
                ->where('date', Carbon::now()->day(1)->format('Y-m-d'))
                ->first();
            if($pgu) {
            
                $assigned = $pgu->assigned;
                $assigned = array_diff($assigned, [$user->id]);
                $assigned = array_values($assigned);
                $pgu->assigned = $assigned;

                $firedx = $pgu->fired;
               
                $firedx[] = $user->id;

                $pgu->fired = array_unique($firedx);
                $pgu->save();

            }
        }
        
        
        if ($user) {
            $user->deleted_at = Carbon::now();
            $user->last_group = json_encode($_groups);

            

            if($request->day && $request->month) $user->deleted_at = Carbon::createFromDate(date('Y'), $request->month, $request->day); // ->format('Y-m-d');

            $email = $user->email;
            $user->save();


            self::setDay($user->id);


            $user->delete();

            /***** */
            $ud = UserDescription::where([
                'user_id' => $user->id,
                'is_trainee' => 0,
            ])->first();

            $bitrix = new Bitrix();
            
            $bitrix_id = 0;
            if($ud && $ud->bitrix_id != 0) {
                $bitrix_id = $ud->bitrix_id;
            } else {
                $bitrixUser = $bitrix->searchUser($email);
                if($bitrixUser) $bitrix_id = $bitrixUser['ID'];
            }
            
            /** Увольнение с Битрикс */
            $success = false;
            if($bitrix_id != 2 && $bitrix_id != 0) $success = $bitrix->deleteUser($bitrix_id); // Нельзя удалять 2 user. Через него работают запросы
            if($success) {
                // Уволен с битрикса
            } else {
                // Не уволен
            }

                
            if($user->phone && $ud) {


                $whatsapp = new IC();
                $wphone = Phone::normalize($user->phone);
                if($wphone) $whatsapp->send_msg($wphone, 'Уважаемый коллега! Какими бы ни были причины расставания, мы благодарим Вас за время, силы, знания и энергию, которые Вы отдали для успешной работы и развития нашей организации, и просим заполнить эту небольшую анкету. %0a https://bp.jobtron.org/quiz_after_fire?phone='. $wphone);
                    
                if($bitrix_id != 0) {
                    $ud->bitrix_id = 0;
                    $ud->save();
                }
                $ud->fired = now();
                $ud->save();
            }

            return back()->withSuccess('Успешно удален');
        } else {
            return back()->withErrors('Пользователь не найден');
        }
    }

    private static function setDay($user_id) {
        $targetUser = User::find($user_id);
        
        $authUser = Auth::user();
        if(!$authUser) $authUser = User::find(5);
        if($targetUser == null) {return ['success' => 1, 'history' => null];}

        $daytype = DayType::where('user_id', $user_id)->whereDate('date', date('Y-m-d'))->first();
     
        if (!$daytype) {
            $daytype = DayType::create([
                'user_id' => $user_id,
                'type' => 4, // Уволен
                'email' => $targetUser->email,
                'date' => date('Y-m-d'),
                'admin_id' => $authUser->id,
            ]);
            $description = 'с обычного на ' . DayType::DAY_TYPES_RU[4];
        } else {
            $description = 'с ' . DayType::DAY_TYPES_RU[$daytype->type] . ' на ' . DayType::DAY_TYPES_RU[4];
            $daytype->type = 4;
            $daytype->admin_id = $authUser->id;
            $daytype->save();
        }

        $authorName = $authUser->name . ' ' . $authUser->last_name;
        $history = TimetrackingHistory::create([
            'user_id' => $user_id,
            'author_id' => $authUser->id,
            'author' => $authorName,
            'date' => date('Y-m-d'),
            'description' => 'Сотрудник уволен рекрутером',
        ]);
    }
    /**
     * Обноляет баланс после платеже
     * @param $amount
     * @return float
     */
    public static function addBalance($user_id, $payment)
    {
        if ($user_id > 0) {
            $user_balance = self::balanceByUser($user_id);
            $user_balance += $payment;

            User::where('id', $user_id)
                ->update(['UF_BALANCE' => $user_balance]);

            //DB::update('UPDATE b_uts_user SET UF_BALANCE = ? WHERE VALUE_ID = ?', [$user_balance, $user_id]);
        }

        return self::balanceByUser($user_id);
    }

    /**
     * @param $bonus
     * @param $user_id
     * @return float
     */
    public static function updateBonus($bonus, $user_id)
    {
        DB::update('UPDATE users SET bonus = bonus + ? WHERE ID = ?', [$bonus, $user_id]);
        return true;
    }

    public static function substractBonus($cost, $user_id)
    {
        DB::update('UPDATE users SET bonus = bonus - ? WHERE ID = ?', [$cost, $user_id]);
        return true;
    }

    public static function userByEmail($user_email)
    {
        $user = User::whereRaw('LOWER(TRIM(email)) = "' . strtolower(trim($user_email)) . '"')->first();
        return $user;
    }

    public static function generateRandomString($length = 8)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * Возвращает баланс текущего пользователя
     * @return float
     */
    public static function balance()
    {
        //$balance = DB::select("SELECT * FROM b_uts_user WHERE VALUE_ID = ?", [self::bitrixUser()->id]);

        $user = User::find(self::bitrixUser()->id);

        if (isset($user)) {
            return $user->UF_BALANCE;
        }
        return 0;
    }

    public static function logo()
    {
        $user = User::find(self::bitrixUser()->id);
        if (!$user->UF_LOGO) {
            return '/static/images/userlogo.jpg';
        }
        return $user->UF_LOGO;
    }

    public static function balanceByUser($user_id)
    {
        //$user_info = DB::select("SELECT * FROM b_uts_user WHERE VALUE_ID = ?", [$user_id]);
        $user = User::find($user_id);
        return isset($user) ? $user->UF_BALANCE : 0;
    }

    public static function isUserAdmin($user_id)
    {
        //$user_info = DB::select("SELECT * FROM b_uts_user WHERE VALUE_ID = ?", [$user_id]);
        $user = User::find($user_id);
        return (isset($user) ? $user->UF_ADMIN : 0) == 1;
    }

    public function course_results()
    {
        return $this->hasMany(\App\Models\CourseResult::class, 'user_id');
    }

    public function user_courses()
    {
        return $this->hasMany(\App\Models\UserCourse::class, 'user_id');
    }

    public function test_results()
    {
        return $this->hasMany(\App\Models\TestResult::class, 'user_id');
    }

    public static function getApiKey()
    {
        $user_id = self::bitrixUser()->id;
        $apiKey = md5($user_id . 'api.mediasend' . '_salt');

        if (User::where('id', $user_id)->whereNull('UF_API_KEY')->count() > 0) {
            User::where('id', $user_id)
                ->update(['UF_API_KEY' => $apiKey]);
        }

        return self::getApiKeyByUserId($user_id);
    }

    public static function getApiKeyByUserId($user_id)
    {
        //$user_info = DB::select("SELECT * FROM b_uts_user WHERE VALUE_ID = ?", [$user_id]);
        $user = User::find($user_id);
        return isset($user) ? $user->UF_API_KEY : '';
    }

    public static function getUserIdApiKey($api_key)
    {
        //$user_info = DB::select("SELECT * FROM b_uts_user WHERE UF_API_KEY = ?", [$api_key]);

        $user = User::where('UF_API_KEY', $api_key)->first();
        if (isset($user)) {
            return $user->id;
        }
        return false;
    }

    public static function getSipAccount($user_id = null)
    {
        //$user_info = DB::select("SELECT * FROM b_uts_user WHERE VALUE_ID = ?", [$user_id?$user_id:self::bitrixUser()->id]);

        $user = User::find($user_id ? $user_id : self::bitrixUser()->id);
        return isset($user) ? $user->UF_SIP_ACC : null;
    }

    public static function updateSipAccount($user_id, $sip_acc)
    {
        User::where('id', $user_id)
            ->update(['UF_SIP_ACC' => $sip_acc]);
        //DB::update('UPDATE b_uts_user SET UF_SIP_ACC = ? WHERE VALUE_ID = ?', [$sip_acc, $user_id]);
        return true;
    }

    public static function getSMPPAccount($user_id = null)
    {
        //$user_info = DB::select("SELECT * FROM b_uts_user WHERE VALUE_ID = ?", [$user_id?$user_id:self::bitrixUser()->id]);

        $user = User::find($user_id ? $user_id : self::bitrixUser()->id);
        return isset($user) ? $user->UF_SMPP : null;
    }

    public static function updateSMPPAccount($user_id, $smpp)
    {

        User::where('id', $user_id)
            ->update(['UF_SMPP' => $smpp]);

        //DB::update('UPDATE b_uts_user SET UF_SMPP = ? WHERE VALUE_ID = ?', [$smpp, $user_id]);
        return true;
    }

    public static function randString($pass_len = 10, $pass_chars = false)
    {
        static $allchars = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
        $string = "";
        if (is_array($pass_chars)) {
            while (strlen($string) < $pass_len) {
                if (function_exists('shuffle')) {
                    shuffle($pass_chars);
                }

                foreach ($pass_chars as $chars) {
                    $n = strlen($chars) - 1;
                    $string .= $chars[mt_rand(0, $n)];
                }
            }
            if (strlen($string) > count($pass_chars)) {
                $string = substr($string, 0, $pass_len);
            }

        } else {
            if ($pass_chars !== false) {
                $chars = $pass_chars;
                $n = strlen($pass_chars) - 1;
            } else {
                $chars = $allchars;
                $n = 61; //strlen($allchars)-1;
            }
            for ($i = 0; $i < $pass_len; $i++) {
                $string .= $chars[mt_rand(0, $n)];
            }

        }
        return $string;
    }

    public function autocalls()
    {
        return $this->hasMany('App\Autocall');
    }

    public function ai_dialings()
    {
        return $this->hasMany('App\AI_dialing');
    }

    public function contacts()
    {
        return $this->hasMany('App\Contact');
    }

    public function trainee() // стажер ли 
    {
        return $this->hasMany('App\Trainee');
    }

    public function sms()
    {
        return $this->hasMany('App\Message');
    }

    public function voices()
    {
        return $this->hasMany('App\Voice');
    }

    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    public function fines()
    {
        return $this->belongsToMany('App\Fine', 'user_fines')->withPivot('day');
    }

    public function daytypes()
    {

        return $this->hasMany('App\DayType', 'user_id');

    }

    public function partner()
    {
        return $this->hasOne('App\Partner', 'user_id', 'id');
    }

    public function zarplata()
    {
        return $this->hasOne('App\Zarplata', 'user_id', 'id');
    }

    public function downloads()
    {
        return $this->hasOne('App\Downloads', 'user_id', 'id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class, 'user_id');
    }

    public function profileContacts()
    {
        return $this->hasMany(UserContact::class, 'user_id');
    }

    public function getSalaryByMonth($date)
    {
        return $this->salaries()->whereYear('date', $date->format('Y'))->whereMonth('date', $date->format('m'))->get();
    }

    public function getSalaryByDay($date)
    {
        return $this->salaries()->whereDate('date', '=', date('Y-m-d'))->orderBy('created_at', 'desc')->first();
    }

    public function getCurrentSalary()
    {
        $tz = Setting::TIMEZONES[$this->timezone];
        $date = \Carbon\Carbon::now($tz);
        $salaries = $this->getSalaryByMonth($date);
        $user_applied_at = $this->applied_at();

        $salary_table = Salary::salariesTable(-1, $date->format('Y-m-d'), [$this->id]);
        
        $sum = 0;
        if(count($salary_table['users']) > 0) {
            $arr = $salary_table['users'][0];
            for($i =1;$i<=$date->daysInMonth;$i++) {
             
                $sum += $arr->earnings[$i] ?? 0;

                $sum -= $arr->fines->where('date', $i)->sum('penalty_amount');


                $sum -= $arr->avanses[$i] ?? 0;

                // if($arr->edited_bonus == null) {
                //     $sum += $arr->bonuses[$i] ?? 0;
                // }
            
                //$sum += $arr->awards[$i] ?? 0;
                //$sum += $arr->test_bonuses[$i] ?? 0;
                
            }   

            // if($arr->edited_kpi == null) {
            //     $sum += $arr->kpi;
            // } 
            // else {
            //     $sum += $arr->edited_kpi->amount;
            // }  

            // if($arr->edited_bonus) {
            //     $sum += $arr->edited_bonus->amount;
            // }

            // if($arr->edited_salary) {
            //     $sum = $arr->edited_salary->amount;
            // }
        }
        
        
        
        return $sum; 
    }

    public function getActiveCourse()
    {
        $c = CourseResult::activeCourse();
        return $c ? CourseResult::with('course')->find($c->id) : null;
    }

    public function getActiveCourses()
    {
        return CourseResult::activeCourses();
    }

    public function getCurrentKpi()
    {

    }

    public function timetracking()
    {
        return $this->hasMany('App\Timetracking', 'user_id', 'id');
    }

    /**
     * Date of apply of user
     * @return date
     */
    public function applied_at()
    {
        $user_applied_at = null;
        $ud = UserDescription::where('user_id', $this->id)->first();
        if($ud && $ud->applied) {
            $user_applied_at = $ud->applied;
        } 
        
        if($user_applied_at == null) {
            $user_applied_at = $this->created_at; 
        }
        
        return $user_applied_at;
    }

    

    public function trackHistory()
    {
        return $this->hasMany('App\TimetrackingHistory', 'user_id', 'id')->orderBy('id', 'DESC');
    }

    public function obtainedBonuses()
    {
        return $this->hasMany('App\Models\Admin\ObtainedBonus', 'user_id', 'id')->orderBy('id', 'DESC');
    }

    public function testBonuses()
    {
        return $this->hasMany('App\Models\TestBonus', 'user_id', 'id')->orderBy('id', 'DESC');
    }

    public static function set_timezone_of($user_id)
    {
        $user = User::find($user_id);

        $offset = 0; // GMT offset
        if ($user) {
            $offset = $user->timezone;
        }

        //$timezone_name = timezone_name_from_abbr('', $offset * 3600, false); // e.g. "America/New_York"
        //date_default_timezone_set($timezone_name);

        date_default_timezone_set('Etc/GMT' . sprintf('%+d', $offset * -1));

        DB::statement("SET time_zone='" . sprintf('%+d:00', $offset) . "'");

        return sprintf('%+d', $offset);
    }

    public static function now_from_db()
    {
        $results = DB::select(DB::raw('SELECT NOW() AS time'));
        return $results[0]->time;
    }

    public static function now_from_php()
    {
        return date('Y-m-d H:i:s', time());
    }

    public function profileGroups()
    {
        $groups = ProfileGroup::where('active', 1)->get();

        $user_groups = [];

        foreach ($groups as $group) {
            $group_users = json_decode($group->users);
            if (!is_array($group_users)) {
                continue;
            }
            $group_users = array_unique($group_users);

            if (in_array($this->id, $group_users)) {
                $user_groups[] = $group;
            }
        }

        return collect($user_groups);

    }

    /**
     * Связь с моделью WorkingTime
     *
     * @return Eloquent
     */
    public function workingTime()
    {
      return $this->belongsTo('App\WorkingTime');
    }

    /**
     * Выходные
     *
     * @return array
     */
    public function weekend()
    {
      return (int)$this->working_day_id === 1 ? [6, 7] : [7];
    }

    /**
     * Время начала смены для юзера
     *
     * @return array
     */
    public function work_starts_at()
    {
        $workStart = '00:00:00';

        if (!is_null($this->work_start)) {

            $workStart = $this->work_start;

        } else {

            $userGroups = ProfileGroup::get();
            foreach ($userGroups as $group) {

                $usersInGroup = explode(',', trim($group->users, '[]'));
                foreach ($usersInGroup as $userIDInGroup) {
                    if ($this->id == $userIDInGroup) {
                        $workStart = $group->work_start;
                        break;
                    }
                }
            }
        }

        return $workStart;
    }

    /**
     * Ставка стажировочных дней
     * Если стаж не оплачивается, то 0
     */
    public function internshipPayRate()
    {
        $groups = ProfileGroup::userIn($this->id);
        $rate = 0;
        foreach ($groups as $key => $group_id) {
            $group = ProfileGroup::find($group_id);
            if($group && $group->paid_internship == 1) {
                $rate = 0.5;
                break;
            }
        }
  
        return $rate;
    }

    /**
     * читал ли корп книгу сегодня 
     */
    public function readCorpBook()
    {
        $ud = UserDescription::where('user_id', $this->id)->first();
        $read = true;
        if($ud && $ud->is_trainee == 0) {
            if($this->read_corp_book_at) {
                $date = Carbon::parse($this->read_corp_book_at)->startOfDay();
                $read = Carbon::now()->startOfDay()->timestamp - $date->timestamp >= 86400 ? false : true;
            } else {
                $read = false;
            }
        }
       
        
        
        return $read;
    }

    /**
     * 
     */
    public function isStartedDay() {
        $tt = Timetracking::whereDate('enter', date('Y-m-d'))
            ->where('user_id', $this->id)
            ->first();
        return $tt ? true : false;
    }

    /**
     *  Check today isnt weekday
     */
    public function canWorkThisDay()
    {
        return $this->weekdays[(int)date('w')] == '1';
    }

    public function created_checklists(){
        return $this->hasMany(\App\Models\Checklist::class,'creator_id','id');
    }

    public function checklists(){
        return $this->belongsToMany(\App\Models\Checklist::class);
    }
}
