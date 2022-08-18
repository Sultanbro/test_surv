<?php

namespace App\Service;

use App\Models\Analytics\UserStat;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiStatisticService
{
    /**
     * Фильтры!
     *
     * Любая дата.
     */
    const ANY_DATE = 0;

    /**
     * Вчера.
     */
    const YESTERDAY = 1;

    /**
     * Сегодня.
     */
    const TODAY = 2;

    /**
     * Вчера.
     */
    const TOMORROW = 3;

    /**
     * Текущий месяц.
     */
    const CURRENT_MONTH = 4;

    /**
     * МЕСЯЦ.
     */
    const MONTH = 5;

    /**
     * Диапазон.
     */
    const RANGE = 6;

    /**
     * С фронта прилитает тип метода подробнее в CalculateKpiService
     * @param Request $request
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function get(Request $request, User $user)
    {
        $method = $request->input('method');
        $date   = $request->input('date');

        return $this->calculateStatistics($user, $method, $date);
    }

    public function fetch(Request $request)
    {
        $parameters = $request->all();
        $groupId    = $parameters['filter']['group_id'] ?? null;

        $group      = ProfileGroup::query()->findOrFail($groupId);
        $users      = $this->getUserKpis($group, $parameters);
        return $users;
    }

    /**
     * Получаем статистику для пользователя и kpi и kpi_items для пользователя
     * и получаем все нужные данные
     * @value kpi_id
     * @value activity_id
     * @value daily_pan
     * @value value
     * @value workdays
     * @value is_user_full_time
     * @param int $userId
     * @param $date
     * @return array
     */
    private function getUserStatistics(int $userId, $date): array
    {
        $userStats = User::query()
            ->join('user_stats', 'users.id', '=', 'user_stats.user_id')
            ->where('users.id', $userId)
            ->groupByRaw('MONTH(user_stats.created_at), user_stats.user_id, user_stats.activity_id, users.full_time, users.working_day_id')
            ->select(
                DB::raw('sum(user_stats.value) as total_fact'),
                'user_stats.user_id',
                'user_stats.activity_id',
                'users.full_time',
                'users.working_day_id'
            )->get()->toArray();

        $kpItems = Kpi::query()
            ->join('kpi_items', 'kpis.id', '=', 'kpi_items.kpi_id')
            ->where([
            ['targetable_id', '=', $userId],
            ['targetable_type', '=', 'App\User']
        ])->get()->toArray();

        $statistics = [];

        foreach ($userStats as $userStat)
        {
            foreach ($kpItems as $userPlan)
            {
                if ($userStat['activity_id'] == $userPlan['activity_id']){
                    $workdays = $userStat['working_day_id'] != 1 ? [6,0] : [0];
                    $statistics[] = [
                        'kpi_id'                    => $userPlan['kpi_id'],
                        'activity_id'               => $userStat['activity_id'],
                        'daily_plan'                => $userPlan['plan'],
                        'total_fact'                => $userStat['total_fact'],
                        'is_user_full_time'         => $userStat['full_time'],
                        'workdays'                  => workdays(date('Y'), date('m'), $workdays),
                        'days_from_user_applied'    => 0,
                        'records_count'             => $this->getRecordsCount($date, $userId),
                        'lower_limit'               => $userPlan['lower_limit'],
                        'upper_limit'               => $userPlan['upper_limit'],
                        'share'                     => $userPlan['share'],
                        'completed_80'              => $userPlan['completed_80'],
                        'completed_100'             => $userPlan['completed_100']
                    ];
                }
            }
        }

        return $statistics;
    }

    /**
     * @param User $user
     * @param $method
     * @param $date
     * @return array
     * @throws Exception
     */
    private function calculateStatistics(User $user, $method, $date): array
    {
        $statistics = $this->getUserStatistics((int) $user->id, $date);
        $calculateKpi = new CalculateKpiService();

        foreach ($statistics as $index => $statistic)
        {
            $statistics[$index]['percent']    = $calculateKpi->getCompletePercent($statistic, $method);
            $statistics[$index]['premiumSum'] = $this->sumOfActivity(
                $statistic['lower_limit'],
                $statistic['upper_limit'],
                $calculateKpi->getCompletePercent($statistic, $method),
                $statistic['share'],
                $statistic['completed_80'],
                $statistic['completed_100']
            );
        }

        return $statistics;
    }

    /**
     * @param $group
     * @param $parameters
     * @return array
     */
    private function getUserKpis($group, $parameters): array
    {
        $userIds = $group->users()->pluck('id')->toArray();

        return User::query()->whereIn('id', $userIds)->whereHas('kpis')->with([
            'kpis' => function($kpi) use($parameters) {
                switch (isset($parameters['filter']['created_at']['variant'])){
                    case $parameters['filter']['created_at']['variant'] == self::ANY_DATE:
                        return $kpi->whereDate('created_at', '2022-08-13');
                    case $parameters['filter']['created_at']['variant'] == self::YESTERDAY:
                        return $kpi->whereDate('created_at', Carbon::yesterday());
                    case $parameters['filter']['created_at']['variant'] == self::TODAY:
                        return $kpi->whereDate('created_at', Carbon::today());
                    case $parameters['filter']['created_at']['variant'] == self::TOMORROW:
                        return $kpi->whereDate('created_at', Carbon::tomorrow());
                    case $parameters['filter']['created_at']['variant'] == self::CURRENT_MONTH:
                        return $kpi->whereMonth('created_at', Carbon::now()->month);
                    case $parameters['filter']['created_at']['variant'] == self::MONTH:
                        return $kpi->whereMonth('created_at', '=', $parameters['filter']['created_at']['month'])
                            ->whereYear('created_at', '=', $parameters['filter']['created_at']['year']);
                    case $parameters['filter']['created_at']['variant'] == self::RANGE:
                        return $kpi->whereDate('created_at', '>', $parameters['filter']['created_at']['from'])
                            ->whereDate('created_at', '<', $parameters['filter']['created_at']['to']);
                }
            }
        ])->get()->toArray();
    }

    /**
     * @param array $date
     * @param int $userId
     * @return int
     */
    private function getRecordsCount(array $date, int $userId): int
    {
        return UserStat::query()->where('user_id', $userId)->when(!empty($date), function ($kpi) use ($date) {
            $kpi->whereYear('created_at', $date['year'])->whereMonth('created_at', $date['month']);
        })->count();
    }

    /**
     * @param int $lower_limit
     * @param int $upper_limit
     * @param float $completed_percent
     * @param int $share
     * @param float $completed_80
     * @param float $completed_100
     * @return float|int
     */
    private function sumOfActivity(
        int $lower_limit,
        int $upper_limit,
        float $completed_percent,
        int $share,
        float $completed_80,
        float $completed_100): float|int
    {
        $result = 0;
        $completed_percent = 80;
        $lower_limit = $lower_limit / 100;
        $upper_limit = $upper_limit / 100;
        $completed_percent = $completed_percent / 100;
        $share = $share / 100;

        if($completed_percent > $lower_limit) {
            if ($completed_percent < $upper_limit) {
                $result = $completed_80 * $share * ($completed_percent - $lower_limit) * $upper_limit / ($upper_limit - $lower_limit);
            } else {
                $result = $completed_100 * $share * $completed_percent;
            }
        } else {
            $result = 0;
        }


        if ($result < 0) {
            $result = 0;
        }
        return $result;
    }
}