<?php

namespace App;

use App\Classes\Helpers\Phone;
use App\Api\BitrixOld as Bitrix;
use App\Http\Controllers\Services\IntellectController as IC;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedSalary;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedBonus;
use App\Models\Anviz\Time;
use App\Models\Article\Article;
use App\Models\Award\Award;
use App\Models\CentralUser;
use App\Models\CourseResult;
use App\Models\GroupUser;
use App\Models\Permission;
use App\Models\Tax;
use App\Models\Traits\HasTenants;
use App\Models\User\Card;
use App\OauthClientToken as Oauth;
use App\Service\Department\UserService;
use App\Traits\CurrencyTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\Permission\Traits\HasRoles;
use App\Models\WorkChart\WorkChartModel;
use App\Models\WorkChart\Workday;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class User extends Authenticatable implements Authorizable
{
    use Notifiable,
        SoftDeletes,
        HasFactory,
        HasRoles,
        HasTenants;

    const USER_TYPE_OFFICE = 'office';
    const USER_TYPE_REMOTE = 'remote';
    const OWNER_ID = 18;

    protected $table = 'users';

    public $timestamps = true;

    protected $primaryKey = 'id';

    protected $hidden = [
        'password'
    ];

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
        'birthday',
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
        'work_chart_id'
    ];

    protected $casts = [
        'timezone' => 'float',
    ];

    /**
     * Валюты для профиля.
     */
    const CURRENCY = ['KZT', 'RUB', 'UZS', 'KGS','BYN', 'UAH'];

    /**
     * @param Builder $query
     * @param string $email
     * @return Builder
     */
    public function scopeGetByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * @param int $id
     * @return User
     */
    public static function getAuthUser(int $id): User
    {
        /** @var User $user */
        $user = self::query()->findOrFail($id);
        return $user;
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('is_access')
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'user_id');
    }

    public function cabinets(): Collection
    {
        $centralUser = CentralUser::with('cabinets')->where('email', $this->email)->first();

        return $centralUser
            ? $centralUser->cabinets->map(function ($user) {
                return $user->only(['user_id', 'tenant_id', 'owner']);
            })
            : collect([]);
    }

    public function favouriteArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_favourites_users',
            'user_id',
            'article_id',
        );
    }
    public function pinnedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_pins_users',
            'user_id',
            'article_id',
        );
    }
    public function views(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_views_users',
            'user_id',
            'article_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'user_tax')
            ->withPivot(['created_at'])
            ->withTimestamps();
    }

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class)
            ->withTimestamps();
    }

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
     * Получить всех стажеров которые ответственен.
     * @return HasMany
     */
    public function trainees(): HasMany
    {
        return $this->hasMany('App\Models\Attendance', 'user_id', 'id');
    }

    public function kpi_obtained_bonuses(): HasMany
    {
        return $this->hasMany('App\Models\Admin\ObtainedBonus', 'user_id', 'id');
    }

    public function edited_salaries(): HasMany
    {
        return $this->hasMany('App\Models\Admin\EditedSalary', 'user_id', 'id');
    }

    public function edited_kpi(): HasMany
    {
        return $this->hasMany('App\Models\Admin\EditedKpi', 'user_id', 'id');
    }

    public function edited_bonuses(): HasMany
    {
        return $this->hasMany('App\Models\Admin\EditedBonus', 'user_id', 'id');
    }

    public function saved_kpi(): HasMany
    {
        return $this->hasMany('App\SavedKpi', 'user_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany('App\ProfileGroup', 'group_user', 'user_id', 'group_id')
            ->withPivot([
                'created_at',
                'updated_at',
                'deleted_at',
                'from',
                'to',
                'status',
            ])->withTimestamps();
    }
    /**
     * Mutator's
     */

    // /**
    //  * @param $value
    //  * @return void
    //  */
    // public function setPasswordAttribute($value): void
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }

    /**
     * @param $value
     * @return void
     */
    public function setNewEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    /* End Mutator's */


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
     * Проверка пользователя на стажера.
     *
     * @param $query
     * @param $userId
     * @return bool
     */
    public function scopeIsTrainee($query, $userId): bool
    {
        return $query->find($userId)->description()->first()->is_trainee == 1;
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
     *  Посчитать фот на одного пользователя
     * */
    public function calculateFot($internship_pay_rate, $date){
        $earningSum = 0;
        $bonusesSum = 0;
        $month = $date->startOfMonth();

        $user_applied_at = $this->applied_at();
        $trainee_days = $this->daytypes->whereIn('type', [5,6,7]);
        $work_shift = $this->working_time_id == 1 ? 8 : 9;

        $tts_before_apply = $this->timetracking
            ->where('time', '<', Carbon::parse($user_applied_at)->timestamp);
        $tts = $this->timetracking
            ->where('time', '>=', Carbon::parse($user_applied_at)->timestamp);

        for ($i = 1; $i <= $month->daysInMonth; $i++) {
            $d = (strlen ($i) == 1) ?  '0' . $i  :  '' . $i;
            $daySalary = $this->salaries->where('day', $d)->first();

            // accrual
            $salary = $daySalary->amount ?? 70000;
            $working_hours = $this->workingTime->time ?? 9;
            $ignore = $this->working_day_id == 1 ? [6,0] : [0];
            $workdays = workdays($month->year, $month->month, $ignore);

            $hourly_pay = $salary / $workdays / $working_hours;

            $time_day = $tts->where('day', $i);
            $time_day_before_apply = $tts_before_apply->where('day', $i);
            $time_day_trainee = $trainee_days->where('day', $i);


            if($time_day_trainee->count() > 0) { // день отмечен как стажировка
                $earningSum += round( $hourly_pay * $internship_pay_rate * $work_shift);

            }
            if($time_day->count() > 0) { // отработанное врея есть
                $total_hours = $time_day->sum('total_hours');
                $earningSum += round($total_hours / 60 * $hourly_pay);

            }
            if($time_day_before_apply->count() > 0) {// отработанное врея есть до принятия на работу
                $total_hours = $time_day_before_apply->sum('total_hours');
                $earningSum += round($total_hours / 60 * $hourly_pay);
            }



            //bonuses
            $bonusesSum += $daySalary?->bonus;

            //awards
            $award_date = Carbon::createFromFormat('m-Y', $month->month . '-' . $month->year);
            $bonusesSum += ObtainedBonus::onDay($this->id, $award_date->day($i)->format('Y-m-d'));





        }
        //test bonuses
        $bonusesSum += $this->testBonuses
            ->where('date','>=', now()->format('Y-m-d'))
            ->sum('amount');

        $kpi = SavedKpi::where('user_id', $this->id)
            ->where('date', $date->format('Y-m-d'))
            ->first();

        $kpiTotal = $kpi->total ?? 0;

        return [
            'earnings' => $earningSum,
            'bonuses' => $bonusesSum,
            'kpi' => $kpiTotal
        ];
    }

    /**
     * Работал у нас дней
     */
    public function wasPartOfTeam() {
        if(!$this->user_description) {
            return 0;
        }

        $date = Carbon::parse( $this->user_description->applied )->timestamp;
        $fired = Carbon::parse($this->deleted_at)->timestamp;

        return (int) ($fired - $date) / 86400;
    }

    /**
     * Рабочие дни со дня принятия
     */
    public function workdays_from_applied($date, $workdays = 6) {
        $date = Carbon::parse($date);
        $applied_from = 0;
        if($this->user_description && $this->user_description->applied) {
            $applied = Carbon::parse($this->user_description->applied);

            $year = $applied->year;
            $month = $applied->month;

            if($year == $date->year && $month == $date->month) {
                $exclude = $workdays == 5 ? 2 : 1;
                $applied_from = workdays_diff($applied->format('Y-m-d'), Carbon::parse($date)->endOfMonth()->format('Y-m-d'), $exclude) + 1;
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

    public function user_description(): HasOne
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

        return $zarplata / $workdays / $working_hours;
    }

    /**
     * В каких группах находится user
     * @return ProfileGroup
     */
    public function inGroups($is_head = false)
    {
        $groups = GroupUser::where('user_id', $this->id)
            ->where([
                ['status', 'active'],
                ['is_head', $is_head]
            ])
            ->whereNull('to')
            ->get()
            ->pluck('group_id')
            ->toArray();

        return ProfileGroup::whereIn('id', array_values($groups))
            //->where('active', 1)
            ->select(['id', 'name', 'work_start', 'work_end', 'has_analytics'])
            ->get();
    }

    /**
     * Уволенные группы пользователя.
     *
     * @return array
     */
    public function firedGroups(): array
    {
        return GroupUser::where('status', 'fired')
            ->where('user_id', $this->id)
            ->get()
            ->pluck('group_id')
            ->toArray();
    }

    /**
     * Бывшие (в которых раньше состоял) группы пользователя.
     *
     * @param Carbon|null $filter
     * @return array
     */
    public function droppedGroups(Carbon $date = null): array
    {
        $groupUser = GroupUser::where('status', 'drop')
            ->where('user_id', $this->id);

        if ($date) $groupUser->whereYear('updated_at', $date->year)
            ->whereMonth('updated_at', $date->month);

        return $groupUser->get()
            ->pluck('group_id')
            ->toArray();
    }

    /**
     * В каких группах находится user c условиями оплаты
     * @return array
     */
    public function inGroupsWithTerms()
    {
        $groups = GroupUser::where('user_id', $this->id)
            ->where('status', 'active')
            ->whereNull('to')
            ->get()
            ->pluck('group_id')
            ->toArray();

        return ProfileGroup::whereIn('id', array_values($groups))
            //->where('active', 1)
            ->select(['id', 'name', 'payment_terms', 'show_payment_terms'])
            ->get();
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

    /**
     * Уволить сотрудника
     */
    public static function deleteUser(Request $request)
    {
        $user_id = $request->user_id
            ? $request->user_id
            : $request->id;

        $user = self::withTrashed()->find($user_id);

        if($user == null)  {
            return back()->withErrors('Пользователь не найден');
        }

        $fireDate = $request->day && $request->month
            ? Carbon::createFromDate(date('Y'), $request->month, $request->day)
            : date('Y-m-d');

        if ($user) {

            (new UserService)->fireUser($user->id, $fireDate);

            $user->deleted_at = Carbon::now();

            if($request->day && $request->month) $user->deleted_at = $fireDate;

            $email = $user->email;
            $user->save();

            self::setDay($user->id);

            $user->delete();

            /***** */
            $ud = UserDescription::where([
                'user_id'    => $user->id,
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

                if($wphone) $whatsapp->send_msg($wphone, 'Уважаемый коллега! Какими бы ни были причины расставания, мы благодарим Вас за время, силы, знания и энергию, которые Вы отдали для успешной работы и развития нашей организации, и просим заполнить эту небольшую анкету. %0a https://'.tenant('id').'.jobtron.org/quiz_after_fire?phone='. $wphone);

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

    public static function userByEmail($user_email)
    {
        $user = User::whereRaw('LOWER(TRIM(email)) = "' . strtolower(trim($user_email)) . '"')->first();
        return $user;
    }

    public static function generateRandomString($length = 8)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public static function logo()
    {
        $user = User::find(auth()->id());
        if (!$user->UF_LOGO) {
            return '/static/images/userlogo.jpg';
        }
        return $user->UF_LOGO;
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

    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    public function fines()
    {
        return $this->belongsToMany('App\Fine', 'user_fines')->withPivot(['day', 'status']);
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

    /**
     * Получить баланс сотрудника на текуший месяц
     *
     * @return int|float
     * @throws \Exception
     */
    public function getCurrentSalary()
    {
        $sum = 0;

        $date = \Carbon\Carbon::now();

        // get user salary data
        // return array with one element
        $salary_table = Salary::salariesTable(-1, $date->format('Y-m-d'), [$this->id]);

        // count total
        if(count($salary_table['users']) > 0) {
            $arr = $salary_table['users'][0];
            for($i =1;$i<=$date->daysInMonth;$i++) {

                // earned
                $sum += $arr->earnings[$i] ?? 0;

                // subtract fines
                $sum -= $arr->fines->where('date', $i)->sum('penalty_amount');

                // subtract avans
                $sum -= $arr->avanses[$i] ?? 0;

                // // if user bonus not edited for month on salary page
                // if($arr->edited_bonus == null) {
                //     // bonuses added on salary page for days
                //     $sum += $arr->bonuses[$i] ?? 0;

                //     // bonuses by Department activities
                //     $sum += $arr->awards[$i] ?? 0;

                //     // bonuses for answers in Courses and tests
                //     $sum += $arr->test_bonuses[$i] ?? 0;

                // } else {
                //     $sum += $arr->edited_bonus->amount;
                // }

            }

            // kpi
            // if($arr->edited_kpi == null) {
            //     $sum += $arr->kpi;
            // } else {
            //     $sum += $arr->edited_kpi->amount;
            // }

            // if salary for month edited on salary page
            if($arr->edited_salary) {
                $sum = $arr->edited_salary->amount;
            }
        }

        return $sum;
    }

    /**
     * Получаем итоговую сумму по курсу валюты.
     *
     * @throws Exception
     */
    public function getTotalByCurrency(
        float $price
    ): float
    {
        $userCurrency = strtolower($this->currency);
        $currency = !in_array($userCurrency, ['kzt', 'rub', 'usd']) ? 'usd' : $userCurrency;

        return round(CurrencyTrait::createMultiCurrencyPrice($price)[$currency], 2);
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
     * Связь с моделью WorkingDay
     *
     * @return Eloquent
     */
    public function workingDay()
    {
      return $this->belongsTo('App\WorkingDay');
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
     * @deprecated выпилить с рефактором граффиков
     * @return string
     */
    public function work_starts_at()
    { //TODO Refactor workCharts
        return $this->workTime()['workStartTime'] .':00';
    }

    /**
     * Время смены для юзера
     *
     * @delegate
     * @return array
     */
    public function workTime()
    {
        $userChart = $this->getWorkChart();

        return WorkChartModel::getWorkTime($userChart);
    }

    public function workTimeFast()
    {
        $userChart = $this->getWorkChartFast();

        return WorkChartModel::getWorkTime($userChart);
    }


    /**
     * Количество рабочих дней в неделе по графику.
     *
     * @return array
     */
    public function chartWorkDays(): array
    {
        $userChart = $this->getWorkChart();

        return WorkChartModel::getWorkDay($userChart);
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
        $dayOfWeek = Carbon::now($this->timezone())->dayOfWeek;
        return $this->weekdays[$dayOfWeek] == '0';
    }

    public function created_checklists(){
        return $this->hasMany(\App\Models\Checklist::class,'creator_id','id');
    }

    public function checklists(){
        return $this->belongsToMany(\App\Models\Checklist::class);
    }

    /**
     * working employees
     */
    public function scopeEmployees(Builder $query)
    {
        $query->with('user_description')
              ->whereHas('user_description', function ($query) {
                   $query->where('is_trainee', 0);
              });
    }

    /**
     * @return array
     */
    public function schedule($withOutHalf = false): array
    {
        $timezone = $this->timezone();

        $workTime = $this->workTime();
        $workStartTime = $workTime['workStartTime'];
        $workEndTime = $workTime['workEndTime'];

        $date = Carbon::now($timezone)->format('Y-m-d');

        //TODO: проверить логику, раньше не было число с *.30
        if ($withOutHalf){
            $start = Carbon::parse("$date $workStartTime", $timezone);
        } else {
            $start = Carbon::parse("$date $workStartTime", $timezone)->subMinutes(30.0);
        }
        $end = Carbon::parse("$date $workEndTime", $timezone);

        if ($start->greaterThan($end)) {
            $end->addDay();
        }

        return [
            'start' => $start,
            'end'   => $end
        ];
    }

    public function scheduleFast($withOutHalf = false): array
    {
        $timezone = $this->timezone();

        $workTime = $this->workTimeFast();
        $workStartTime = $workTime['workStartTime'];
        $workEndTime = $workTime['workEndTime'];

        $date = Carbon::now($timezone)->format('Y-m-d');

        //TODO: проверить логику, раньше не было число с *.30
        if ($withOutHalf){
            $start = Carbon::parse("$date $workStartTime", $timezone);
        } else {
            $start = Carbon::parse("$date $workStartTime", $timezone)->subMinutes(30.0);
        }
        $end = Carbon::parse("$date $workEndTime", $timezone);

        if ($start->greaterThan($end)) {
            $end->addDay();
        }

        return [
            'start' => $start,
            'end'   => $end
        ];
    }

    /**
     * Timezone
     *
     * @return String
     */
    public function timezone()
    {
        $user_timezone = ($this->timezone >= 0) ? $this->timezone : 6;
        return Setting::TIMEZONES[$user_timezone];
    }

    /**
     * Cтраница из Базы знаний
     * Показывается при начале дня Сотрудника
     * Сотрудник обязан читать минимум 60 сек
     *
     * @return \App\KnowBase|null;
     */
    public function getCorpbook()
    {
        return !$this->readCorpBook()
            ? \App\KnowBase::getRandomPage()
            : null;
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' ' . $this->last_name;
    }

    public function getImgUrlPathAttribute(): string
    {
        if ($this->img_url){
            return '/users_img/' .$this->img_url;

        }
        return '/user.png';
    }


    public function activeGroup(): ?ProfileGroup
    {
        return $this->groups()->where('status', '=', 'active')->first();
    }

    public function activeGroupFast(): ?ProfileGroup
    {

        if($this->groups){
            return $this->groups->where('pivot.status', '=', 'active')->first();
        }
        return null;
    }

    /**
     * @return Position|null
     */
    public function currentPosition(): ?Position
    {
        return $this->position()->first() ?? null;
    }

    public function workdays(): BelongsToMany
    {
        return $this->belongsToMany(Workday::class, 'user_workday')->withTimestamps();
    }

    /**
     * Получаем график для пользователя.
     *
     * @return BelongsTo
     */
    public function workChart(): BelongsTo
    {
        return $this->belongsTo(WorkChartModel::class);
    }

    /**
     * Получаем дни работы для пользователя за неделю.
     * @return int[]
     */
    public function getCountWorkDays(): array
    {
        $workChart = $this->getWorkChart();
        $floatingDayoffs = $workChart->floating_dayoffs;
        if ($workChart && $workChart->workdays !== null && $floatingDayoffs === 0){
            return WorkChartModel::convertWorkDays($workChart->workdays);
        }

        $type = $floatingDayoffs > 0
            ? (string)WorkChartModel::DAYS_IN_WEEK - $floatingDayoffs."-".$floatingDayoffs
            : $workChart?->name ?? "6-1";


        return match ($type) {
            "6-1" => [0],
            "5-2" => [6,0],
            "4-3" => [5,6,0],
            "3-4" => [4,5,6,0],
            "2-5" => [3,4,5,6,0],
            "1-6" => [2,3,4,5,6,0],
            "1-1", "2-2", "3-3" => [5,6,0],
            default => throw new InvalidArgumentException("Invalid chart type"),
        };
    }

    /**
     * Получаем дни работы для пользователя за месяц
     * @return int
     */
    public function getCountWorkDaysMonth($year = null, $month = null): int {
        $requestDate = Carbon::createFromDate($year, $month);

        if ($this->first_work_day) {
            $firstWorkDay = Carbon::parse($this->first_work_day);
            if ($firstWorkDay->year >= $year && $firstWorkDay->month >= $month) {
                $firstWorkDay = $requestDate->firstOfMonth()->format('Y-m-d');
            }else {
                $firstWorkDay = $firstWorkDay->format('Y-m-d');
            }
        } else {
            $firstWorkDay = $requestDate->firstOfMonth()->format('Y-m-d');
        }

        $workChartName = $this->workChart->name;

        if ($workChartName == "2-2"){
            return WorkChartModel::WORK_DAYS_PER_MONTH_DEFAULT_REPLACEABLE;
        }

        $days = explode('-', $workChartName);
        $workingDay = (int)$days[0];
        $dayOff = (int)$days[1];
        $total = $workingDay + $dayOff;

        if ($year == null && $month == null) {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
        }

        $daysInMonth = $requestDate->daysInMonth;

        $workDayInMonth = 0;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dayInMonth = Carbon::createFromDate($year, $month)->setDay($i)->format('Y-m-d');
            $date1 = date_create($dayInMonth);
            $date2 = date_create($firstWorkDay);
            $differBetweenFirstAndLastDay = date_diff($date1, $date2)->days;
            $remains = $differBetweenFirstAndLastDay % $total;

            if ($remains < $workingDay && $dayInMonth != Carbon::parse($firstWorkDay)->subDay()->toDateString()) {
                $workDayInMonth++;
            }
        }
        return $workDayInMonth;
    }

    public function getWorkChart(): ?WorkChartModel {
        $userChart = $this->workChart()->first();

        if ($userChart) {
            return $userChart;
        }

        $groups   = $this->activeGroup();
        $groupChart = $groups?->workChart()->first();

        return $groupChart;
    }

    public function getWorkChartFast(): ?WorkChartModel {
        $userChart = $this->workChart;

        if ($this->workChart) {
            return $this->workChart;
        }

        $group = $this->activeGroupFast();
        if ($group && $group->workChart) {
            return $group->workChart;
        }

        return null;
    }

    /**
     * @param int $id
     * @return Model
     */
    public static function getUserById(
        int $id
    ): Model
    {
        return self::withTrashed()->findOrFail($id);
    }

    /**
     * @return float
     */
    public function sumQuarterPremiums(): float
    {
        $individualQuarterPremium = $this->qpremium()
            ->where('from', '<=', now()->format('Y-m-d'))
            ->where('to', '>=', now()->format('Y-m-d'))
            ->sum('sum') ?? 0;

        $groupQuarterPremium = $this->activeGroup()->qpremium()
            ->where('from', '<=', now()->format('Y-m-d'))
            ->where('to', '>=', now()->format('Y-m-d'))
            ->sum('sum') ?? 0;

        $positionQuarterPremium = $this->currentPosition()->qpremium()
            ->where('from', '<=', now()->format('Y-m-d'))
            ->where('to', '>=', now()->format('Y-m-d'))
            ->sum('sum') ?? 0;

        return $individualQuarterPremium + $groupQuarterPremium + $positionQuarterPremium;
    }

    /**
     * @return int
     */
    public function countWorkHours(): int
    {
        $schedule = $this->schedule(true);
        $workChart = $this->workChart;
        if ($workChart && $workChart->rest_time != null){
            $lunchTime = $workChart->rest_time;
            $hour = intval($lunchTime / 60);
            $minute = $lunchTime % 60;
            $totalHour = floatval($hour.".".$minute);
            $userWorkHours = max($schedule['end']->diffInSeconds($schedule['start']), 0);
            $working_hours = round($userWorkHours / 3600, 1) - $totalHour;
        }else{
            $lunchTime = 1;
            $userWorkHours = max($schedule['end']->diffInSeconds($schedule['start']), 0);
            $working_hours = round($userWorkHours / 3600, 1) - $lunchTime;
        }


        return $working_hours;
    }

    /**
     * Проверка время и даты, для того чтобы нажать "НАЧАТЬ РАБОЧИЙ ДЕНЬ"
     * @return bool
     * @throws Exception
     */
    public function checkWorkdaysForStartTracking(): bool
    {
        $workChart = $this->getWorkChart();

        if ($workChart->work_charts_type === WorkChartModel::WORK_CHART_TYPE_USUAL && $workChart->workdays !== null) {
            if ($workChart->floating_dayoffs > 0){
                $itemsInWeek = Timetracking::getItemInWeek($this->id);
                if ($itemsInWeek > WorkChartModel::DAYS_IN_WEEK - $workChart->floating_dayoffs){
                    return false;
                }else{
                    return true;
                }
            }

            $day = strrev(decbin($workChart->workdays));

            $numWeek = Carbon::today()->dayOfWeek;
            $numWeek = $numWeek === 0 ? 7 : $numWeek;

            $dayNum = $day[$numWeek - 1] ?? null;
            if ($dayNum == 1) {
                return true;
            }
            return false;
        }
        elseif ($workChart->work_charts_type === WorkChartModel::WORK_CHART_TYPE_REPLACEABLE && $this->first_work_day !== null) {
            $days = explode('-', $workChart->name);
            $workingDay = array_key_exists(0, $days) ? (int)$days[0] : throw new Exception(message: 'Проверьте график работы', code: 400);
            $dayOff = array_key_exists(1, $days) ? (int)$days[1] : throw new Exception(message: 'Проверьте график работы', code: 400);

            $date1 = date_create(now()->format('Y-m-d'));
            $date2 = date_create($this->first_work_day);
            $differBetweenFirstAndLastDay = date_diff($date1, $date2)->days;

            $total = $workingDay + $dayOff;

            if ($workingDay === 1){
                $remains = $differBetweenFirstAndLastDay % $total;
                if ($remains === 0) {
                    return true;
                }
                return false;
            }

            $remains = $differBetweenFirstAndLastDay % $total;
            if ($remains < $workingDay) {
                return true;
            }
            return false;
        }
        return true;
    }

     /**
     * @return bool
     */
    public function isFired(): bool
    {
        dd($this);
        return !($this->deleted_at == null);
    }

    /**
     * Получаем дни работы для пользователя за месяц
     * @param $date
     * @return int
     * @throws Exception
     */
    public function getWorkDays($date): int{
        $workChartType = $this->workChart->work_charts_type ?? 0;
        if ($workChartType == 0 || $workChartType == WorkChartModel::WORK_CHART_TYPE_USUAL){
            $ignore = $this->getCountWorkDays();   // Какие дни не учитывать в месяце
            $workDays = workdays($date->year, $date->month, $ignore);
        }
        elseif ($workChartType == WorkChartModel::WORK_CHART_TYPE_REPLACEABLE) {
            $workDays = $this->getCountWorkDaysMonth($date->year, $date->month);
        }
        else {
            throw new Exception(message: 'Проверьте график работы', code: 400);
        }
        return $workDays;
    }
}
