<?php

namespace App\Repositories\Referral;

use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];

    public function statistic(array $filter): array
    {
        $this->filter = $filter;
        $referrers = $this->referrers();
        return [
            'pivot' => $this->pivot($referrers),
            'referrers' => $referrers
        ];
    }

    protected function pivot(array $referrers): array
    {
        $deal_lead_conversion = 0;
        $applied_deal_conversion = 0;
        $countForDeals = 0;
        $countForApplied = 0;
        $accepted = 0;
        $paidTotal = 0;
        $paidTotalForMonth = 0;
        $earnedTotalForMonth = 0;

        foreach ($referrers as $referer) {
            $accepted += $referer['applieds'];
            $paidTotal += $referer['absolute_paid'];
            $paidTotalForMonth += $referer['month_paid'];
            $earnedTotalForMonth += $referer['month_earned'];
            $deal_lead_conversion += $referer['deal_lead_conversion_ratio'];
            if ($referer['leads'] > 0) {
                ++$countForDeals;
            }
            $applied_deal_conversion += $referer['appiled_deal_conversion_ratio'];
            if ($referer['deals'] > 0) {
                ++$countForApplied;
            }
        }

        $deal_lead_conversion = $deal_lead_conversion / $countForDeals;
        $applied_deal_conversion = $applied_deal_conversion / $countForApplied;

        return [
            'employee_price' => $accepted ? $paidTotal / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $paidTotalForMonth,
        ];
    }

    protected function referrers(): array
    {
        $startDate = $this->dateStart()->format("Y-m-d");
        $endDate = $this->dateEnd()->format("Y-m-d");
        $paidTypeFirstWork = PaidType::FIRST_WORK->name;

        // Aggregate subqueries prepared as joins
        $referralSalariesSubQuery = DB::table('referral_salaries')
            ->select('referrer_id',
                DB::raw("SUM(amount) AS absolute_earned"),
                DB::raw("SUM(CASE WHEN is_paid = 1 THEN amount ELSE 0 END) AS absolute_paid"),
                DB::raw("SUM(CASE WHEN STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate' THEN amount ELSE 0 END) AS month_earned"),
                DB::raw("SUM(CASE WHEN is_paid = 1 AND STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate' THEN amount ELSE 0 END) AS month_paid"),
                DB::raw("SUM(CASE WHEN is_paid = 1 AND STR_TO_DATE(date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate' AND type = '$paidTypeFirstWork' AND  amount < 10000 THEN amount ELSE 0 END) AS referrers_earned")
            )
            ->groupBy('referrer_id');

        $segmentId = LeadTemplate::SEGMENT_ID;

        // SubQuery for counting applieds
        $appliedsSubQuery = DB::table('users as ref')
            ->join('user_descriptions', 'ref.id', '=', 'user_descriptions.user_id')
            ->select('ref.referrer_id', DB::raw('COUNT(*) as total_applieds'))
            ->where('user_descriptions.is_trainee', 0)
            ->groupBy('ref.referrer_id');

        // SubQuery for counting leads
        $leadsSubQuery = DB::table('bitrix_leads')
            ->select('referrer_id', DB::raw('COUNT(*) as total_leads'))
            ->where('segment', $segmentId)
            ->groupBy('referrer_id');

        // SubQuery for counting deals
        $dealsSubQuery = DB::table('bitrix_leads')
            ->select('referrer_id', DB::raw('COUNT(*) as total_deals'))
            ->where('segment', $segmentId)
            ->where('deal_id', '>', 0)
            ->groupBy('referrer_id');

        // Main query with optimized joins

        return User::query()
            ->whereHas('referralLeads')
            ->leftJoinSub($appliedsSubQuery, 'applieds', 'users.id', '=', 'applieds.referrer_id')
            ->leftJoinSub($leadsSubQuery, 'leads', 'users.id', '=', 'leads.referrer_id')
            ->leftJoinSub($dealsSubQuery, 'deals', 'users.id', '=', 'deals.referrer_id')
            ->leftJoinSub($referralSalariesSubQuery, 'referral_salaries', 'users.id', '=', 'referral_salaries.referrer_id')
            ->with(['user_description' => function ($query) {
                $query->select(['id', 'user_id', 'is_trainee']);
            }])
            ->select([
                'users.id',
                'users.referrer_id',
                'users.name',
                'users.last_name',
                'users.referrer_status',
                'users.deleted_at',
                'referral_salaries.absolute_earned',
                'referral_salaries.month_earned',
                'referral_salaries.absolute_paid',
                'referral_salaries.month_paid',
                'referral_salaries.referrers_earned',
                'applieds.total_applieds as applieds',
                'leads.total_leads as leads',
                'deals.total_deals as deals',
            ])
            ->get()
            ->each(function (User $user) {
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
            })
            ->toArray();
    }

    protected function dateStart(): Carbon
    {
        $this->filter['date'] = $this->filter['date'] ?? now()->format("Y-m-d");
        return Carbon::parse($this->filter['date'])
            ->startOfMonth()
            ->copy();
    }

    protected function dateEnd(): Carbon
    {
        $this->filter['date'] = $this->filter['date'] ?? now()->format("Y-m-d");
        return Carbon::parse($this->filter['date'])
            ->endOfMonth()
            ->copy();
    }

    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }
}