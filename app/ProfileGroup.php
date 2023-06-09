<?php

namespace App;

use App\Helpers\FileHelper;
use App\Models\Analytics\Activity;
use App\Models\KnowBaseModel;
use App\Models\WorkChart\WorkChartModel;
use App\ProfileGroup\ProfileGroupUsersQuery;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Books\BookGroup;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property bool $archive_utility
 */
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
        'editors_id', // Кто может редактировать отдел
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
        'show_payment_terms', // показывать в профиле условия оплаты труда,
        'archived_date', // дата последнего архивирование
        'work_chart_id', // График работы группы,
        'archive_utility',
    ];

    CONST IS_ACTIVE = 1;
    CONST NOT_ACTIVE = 0;

    CONST NOT_ANALYTICS = 0;
    CONST HAS_ANALYTICS = 1;
    CONST ARCHIVED      = -1;

    // time_address
    CONST FROM_UCALLS = -1;
    CONST NOWHERE = 0;

    const IT_DEPARTMENT_ID = 26;

    /**
     * @param int $id
     * @return Model
     */
    public static function getById(
        int $id
    ): Model
    {
        return self::query()->findOrFail($id);
    }

    /**
     * @return BelongsTo
     */
    public function workChart(): BelongsTo
    {
        return $this->belongsTo(WorkChartModel::class);
    }

    /**
     * @return MorphMany
     */
    public function knowBases(): MorphMany
    {
        return $this->morphMany(KnowBaseModel::class, 'modelable', 'model_type', 'model_id');
    }

    /**
     * @return MorphMany
     */
    public function qpremium(): MorphMany
    {
        return $this->morphMany('App\Models\QuartalPremium', 'targetable', 'targetable_type', 'targetable_id');
    }

    /**
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'group_id');
    }

    /**
     * @return MorphMany
     */
    public function bonuses(): MorphMany
    {
        return $this->morphMany('App\Models\Kpi\Bonus', 'targetable', 'targetable_type', 'targetable_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'group_user', 'group_id', 'user_id')
            ->withPivot(['from', 'to'])
            ->withTimestamps();
    }

    /**
     * Returns users relation
     * @return BelongsToMany
     */
    public function profileGroupUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }


    /**
     * @return MorphMany
     */
    public function kpis(): MorphMany
    {
        return $this->morphMany('App\Models\Kpi\Kpi', 'targetable', 'targetable_type');
    }

    public function dialer()
    {
        return $this->hasOne('App\Models\CallibroDialer', 'group_id', 'id');
	}

    public function plan()
    {
        return $this->hasOne('App\GroupPlan', 'group_id', 'id');
	}

    /**
     * Получаем активные и архивированные группы, которые попадают под фильтр.
     * @param $query
     * @param $year
     * @param $month
     * @return array
     */
    public function scopeProfileGroupsWithArchived($query, $year, $month, $withArchive = true,  $archivedThisMonth = false): array
    {
        $date = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d');

        $profileGroups = $this->where('active', self::IS_ACTIVE)
            ->whereDate('created_at', '<=', $date)
            ->where(fn($q) => $q->whereNull('archived_date')->orWhere(fn($q) => $q->whereYear('archived_date', '>=', $year)->whereMonth('archived_date', '>=', $month)));

        if($archivedThisMonth){
            $firstDayMonth = Carbon::create($year, $month)->firstOfMonth()->format('Y-m-d');
            $profileGroups->where('has_analytics', self::HAS_ANALYTICS)
                ->orWhere('archived_date', '>=', $firstDayMonth);
        }
        else if ($withArchive){
            $profileGroups->whereIn('has_analytics', [self::HAS_ANALYTICS, self::ARCHIVED]);
        }
        else{
            $profileGroups->where('has_analytics', self::HAS_ANALYTICS);
        }

        $profileGroups->where(fn($group) => $group->whereNull('archived_date')->orWhere(
            fn($q) => $q->whereYear('archived_date', '>=', $year)->whereMonth('archived_date', '>=', $month))
        )->get()->reject(function ($group) {
            if ($group->has_analytics == self::HAS_ANALYTICS && $group->archived_date != null)
            {
                return $group;
            }
            if ($group->has_analytics == self::ARCHIVED && $group->archived_date == null)
            {
                return $group;
            }
        });

        return $profileGroups->pluck('id')->toArray();
    }

    /**
     * Активные группы с аналитикой.
     * @param $query
     * @return array
     */
    public function scopeActiveProfileGroupsWithAnalytics($query): array
    {
        return $this->where([
            ['active', 1],
            ['has_analytics', 1]
        ])->get()->pluck('id')->toArray();
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

    /**
     * @param int $groupId
     * @param ?Carbon|string $date
     * @param ?int $deleteType
     * @param ?array<int> $positionIds
     * @return array<int>
     */
    public static function employees(
        int $groupId,
        $date = null,
        $deleteType = 0,
        $positionIds = [],
    ) {
        if($date) {
            $date = Carbon::parse($date);
        }
        $query = (new ProfileGroupUsersQuery())
            ->whereIsTrainee(false)
            ->deletedByMonthFilter($deleteType, $date)
            ->groupeFilter($groupId, $date);

        if(count($positionIds) > 0) {
            $query->wherePositionIds($positionIds);
        }

        return $query->getUserIds();
    }

    /**
     * @param ?Carbon|string $date
     * @return array<int>
     */
    public function workers($date = null) { 
        if($date) {
            $date = Carbon::parse($date);
        }

        return (new ProfileGroupUsersQuery())
            ->whereIsTrainee(false)
            ->deletedByMonthFilter(0, $date)
            ->groupeFilter($this->id, $date)
            ->getUserIds();
    }

    /**
     * @param ?Carbon|string $date
     * @return array<int>
     */
    public function trainees($date = null) 
    {
        if($date) {
            $date = Carbon::parse($date);
        }

        return (new ProfileGroupUsersQuery())
            ->whereIsTrainee(true)
            ->deletedByMonthFilter(0, $date)
            ->groupeFilter($this->id, $date)
            ->getUserIds();
    }

    /**
     * @return BelongsToMany
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()->wherePivot('status', 'active');
    }

    /**
     * Возвращает группы, которые берут данные о звонках с ucalls.
     *
     * @return self
     */
    public static function getUcallsConnectedGroups()
    {
        return self::select('profile_groups.id', 'callibro_dialers.dialer_id', 'profile_groups.time_exceptions')
            ->where('profile_groups.active', 1)
            ->where('profile_groups.is_ucalls', 1)
            ->leftJoin('callibro_dialers', 'callibro_dialers.group_id', 'profile_groups.id')
            ->get()->each(function ($group){
                $group['activities'] = Activity::select('id')
                    ->where('group_id', $group->id)
                    ->where('is_ucalls', 1)
                    ->get()
                    ->pluck('id')
                    ->toArray();
            });
    }
}
