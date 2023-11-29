<?php

namespace App;

use App\DTO\Top\SwitchTopDTO;
use App\Models\Analytics\Activity;
use App\Models\AnalyticsActivitiesSetting;
use App\Models\Books\BookGroup;
use App\Models\KnowBaseModel;
use App\Models\WorkChart\WorkChartModel;
use App\ProfileGroup\ProfileGroupUsersQuery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property bool $archive_utility
 * @property $name
 * @property $users
 * @property $work_start
 * @property $work_end
 * @property $workdays
 * @property $editors_id
 * @property $required
 * @property $provided
 * @property $head_id
 * @property $bp_link
 * @property $zoom_link
 * @property $checktime
 * @property $checktime_users
 * @property $salary_approved
 * @property $salary_approved_by
 * @property $salary_approved_date
 * @property $active
 * @property $payment_terms
 * @property $has_analytics
 * @property $quality
 * @property $editable_time
 * @property $time_address
 * @property $time_exceptions
 * @property $paid_internship
 * @property $rentability_max
 * @property $show_payment_terms
 * @property $archived_date
 * @property $work_chart_id
 * @property $switch_utility
 * @property $switch_proceeds
 * @property $switch_rentability
 * @property $id
 */
class ProfileGroup extends Model
{
    use HasRoles, HasFactory;

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
        'switch_utility',
        'switch_proceeds',
        'switch_rentability'
    ];

    const IS_ACTIVE = 1;
    const NOT_ACTIVE = 0;

    const NOT_ANALYTICS = 0;
    const HAS_ANALYTICS = 1;
    const ARCHIVED = -1;

    // time_address
    const FROM_UCALLS = -1;
    const NOWHERE = 0;

    const IT_DEPARTMENT_ID = 26;

    const SWITCH_UTILITY = 'switch_utility';
    const SWITCH_PROCEEDS = 'switch_proceeds';
    const SWITCH_RENTABILITY = 'switch_rentability';

    const SWITCH_ON = 1;
    const SWITCH_OFF = 0;

    const IS_EMPLOYEE = 'active';
    const IS_FIRED = 'fired';

    const IS_TRANSFER = 'drop';

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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'group_user', 'group_id', 'user_id')
            ->withPivot(['from', 'to'])
            ->withTimestamps();
    }

    public function usersWithTrashed(): BelongsToMany
    {
        return $this->users()
            ->withTrashed();
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
     * @param $year
     * @param $month
     * @param bool $withArchive
     * @param bool $archivedThisMonth
     * @param string $switchColumn
     * @return array
     */
    public static function profileGroupsWithArchived($year, $month, bool $withArchive = true, bool $archivedThisMonth = false, string $switchColumn = ''): array
    {
        $date = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d');

        $profileGroups = self::query()->where('active', self::IS_ACTIVE)
            ->whereDate('created_at', '<=', $date)
            ->where(fn($q) => $q->whereNull('archived_date')->orWhere(fn($q) => $q->whereYear('archived_date', '>=', $year)->whereMonth('archived_date', '>=', $month)));
        if ($switchColumn !== '') {
            $profileGroups->where($switchColumn, 1); // написано так, чтобы не сломать работающий код
        }

        if ($archivedThisMonth) {
            $firstDayMonth = Carbon::create($year, $month)->firstOfMonth()->format('Y-m-d');
            $profileGroups->where('has_analytics', self::HAS_ANALYTICS)
                ->orWhere('archived_date', '>=', $firstDayMonth);
        } else if ($withArchive) {
            $profileGroups->whereIn('has_analytics', [self::HAS_ANALYTICS, self::ARCHIVED]);
        } else {
            $profileGroups->where('has_analytics', self::HAS_ANALYTICS);
        }

        $profileGroups->where(fn($group) => $group->whereNull('archived_date')->orWhere(
            fn($q) => $q->whereYear('archived_date', '>=', $year)->whereMonth('archived_date', '>=', $month))
        )->get()->reject(function ($group) {
            if ($group->has_analytics == self::HAS_ANALYTICS && $group->archived_date != null) {
                return $group;
            }
            if ($group->has_analytics == self::ARCHIVED && $group->archived_date == null) {
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

    public function groupUsers()
    {

        $user_ids = json_decode($this->users);

        if (json_last_error() === JSON_ERROR_NONE && is_array($user_ids) && count($user_ids)) {
            $user_ids = array_unique($user_ids);
            return User::selectRaw("*,CONCAT(name,' ',last_name) as full_name")->whereIn('id', $user_ids)->get();
        }

        return null;
    }

    public static function addBookgroupsToProfileGroup($profile_id, $groups)
    {
        $profile = ProfileGroup::find($profile_id);

        $group_ids = [];
        foreach ($groups as $group) {
            array_push($group_ids, $group['id']);
        }

        if ($profile) {
            $profile->book_groups = json_encode($group_ids);
            $profile->save();
        }

    }

    public static function getBookGroupsArray($profile_id)
    {
        $profile = self::find($profile_id);

        if ($profile) {
            return json_decode($profile->book_groups) ? json_decode($profile->book_groups) : [];
        } else {
            return [];
        }

    }

    public static function getBookGroups($profile_id)
    {
        $profile = ProfileGroup::find($profile_id);

        if ($profile) {
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

        foreach ($groups_collection as $group) {
            $groups[$group->id] = $group->name;
        }

        return $groups;
    }

    /**
     * Получить руководителей всех групп
     */
    public static function getHeads()
    {
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
    public static function headIn($user_id, $is_array = true)
    {
        $groups = self::get();

        $g = [];

        foreach ($groups as $key => $group) {
            if (in_array($user_id, json_decode($group->head_id))) {
                array_push($g, $group->id);
            }
        }

        if ($is_array) {
            return array_unique($g);
        } else {
            if (count($g) > 0) {
                return self::where('id', array_unique($g))->get();
            } else {
                return collect([]);
            }
        }

    }

    /**
     * User in groups
     */
    public static function userIn($user_id, $is_array = true)
    {
        $groups = self::get();

        $g = [];

        foreach ($groups as $key => $group) {
            if ($group->users == null) continue;
            if (in_array($user_id, json_decode($group->users))) {
                array_push($g, $group->id);
            }
        }

        if ($is_array) {
            return array_unique($g);
        } else {
            return self::whereIn('id', array_unique($g))->get();
        }

    }

    /**
     * @param int $groupId
     * @param ?string|Carbon $date
     * @param ?int $deleteType
     * @param ?array<int> $positionIds
     * @return array<int>
     */
    public static function employees(
        int           $groupId,
        Carbon|string $date = null,
        ?int          $deleteType = 0,
        ?array        $positionIds = [],
    )
    {
        if ($date) {
            $date = Carbon::parse($date)->subMonth();
        }
        $query = (new ProfileGroupUsersQuery())
            ->whereIsTrainee(false)
            ->deletedByMonthFilter($deleteType, $date)
            ->groupeFilter($groupId, $date);

        if (count($positionIds) > 0) {
            $query->wherePositionIds($positionIds);
        }

        return $query->getUserIds();
    }

    /**
     * @param ?Carbon|string $date
     * @return array<int>
     */
    public function workers($date = null)
    {
        if ($date) {
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
        if ($date) {
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
            ->get()->each(function ($group) {
                $group['activities'] = Activity::select('id')
                    ->where('group_id', $group->id)
                    ->where('is_ucalls', 1)
                    ->get()
                    ->pluck('id')
                    ->toArray();
            });
    }

    /**
     * Получаем список активных группы с аналитикой в зависимости от switch_******
     * @return self
     */
    public static function getActiveProfileGroupsAnyAnalytics()
    {
        return self::where('active', 1)
            ->where('has_analytics', self::HAS_ANALYTICS)
            ->get();
    }

    public static function updateSwitch(SwitchTopDTO $dto)
    {
        return self::findOrFail($dto->id)
            ->update([
                $dto->switchColumn => $dto->switchValue
            ]);
    }

    public function activitiesSetting()
    {
        return $this->belongsTo(AnalyticsActivitiesSetting::class, 'activities_setting_id', 'id');
    }

    public function scopeWhereHasAnalytics($query)
    {
        return $query->whereIn('has_analytics', [self::HAS_ANALYTICS, self::NOT_ANALYTICS]);
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', self::IS_ACTIVE);
    }

    public function scopeIsArchived($query)
    {
        return $query->where('has_analytics', self::ARCHIVED);
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return Builder
     */
    public function actualAndFiredEmployees(
        string $dateFrom,
        string $dateTo
    ): Builder
    {
        return User::query()
            ->join('group_user as p', fn($join) => $join->on('p.user_id', '=', 'users.id'))
            ->join('profile_groups as g', fn($join) => $join->on('p.group_id', '=', 'g.id'))
            ->join('user_descriptions as d', fn($join) => $join->on('d.user_id', '=', 'users.id'))
            ->select([
                'users.id as id',
                'users.name as name',
                'users.last_name as last_name',
                'users.full_time as full_time',
                'users.email as email',
                'users.deleted_at as deleted_at',
                'd.is_trainee as is_trainee',
                'g.id as group_id',
                'g.name as group_name'
            ])
            ->where('group_id', $this->getKey())
            ->where(fn($query) => $query
                ->whereDate('users.deleted_at', '>=', $dateFrom)
                ->orWhereNull('users.deleted_at')
            )
//            ->whereDate('p.from', '>=', $dateFrom)
//            ->where(fn($query) => $query
//                ->whereDate('p.to', '<=', $dateTo)
//                ->orWhereNull('p.to')
//            )
            ->where('d.is_trainee', 0)
            ->where('g.status', 'active')
            ->distinct()
            ->orderBy('last_name')
            ->orderBy('name');
    }
}
