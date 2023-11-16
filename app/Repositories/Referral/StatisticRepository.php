<?php

namespace App\Repositories\Referral;

use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];


    public function statistic(array $filter): array
    {
        $this->filter = $filter;
        $described = $this->described();
        return [
            'pivot' => $this->pivot($described),
            'referrers' => $described
        ];
    }

    protected function pivot(array $described): array
    {
        $deal_lead_conversion = 0;
        $applied_deal_conversion = 0;
        $countForDeals = 0;
        $countForApplied = 0;

        foreach ($described as $referer) {
            $deal_lead_conversion += $referer['deal_lead_conversion_ratio'];
            if ($referer['leads'] > 0) {
                ++$countForDeals;
            }
            $applied_deal_conversion += $referer['appiled_deal_conversion_ratio'];
            if ($referer['deals'] > 0) {
                ++$countForApplied;
            }
        }

        $deal_lead_conversion = $countForDeals ? $deal_lead_conversion / $countForDeals : 0;
        $applied_deal_conversion = $countForApplied ? $applied_deal_conversion / $countForApplied : 0;

        $accepted = User::withTrashed()
            ->whereRelation('description', 'is_trainee', 0)
            ->whereNotNull('referrer_id')
            ->count();

        $salaries = ReferralSalary::query()->get();

        $paidTotal = $salaries
            ->where('is_paid', 1) // this means that salary was accepted!
            ->sum('amount');

        $paidTotalForMonth = $salaries
            ->where('date', '>=', $this->dateStart()->format("Y-m-d"))
            ->where('date', '<=', $this->dateEnd()->format("Y-m-d"))
            ->where('is_paid', 1) // this means that salary was accepted!
            ->sum('amount');

        $earnedTotalForMonth = $salaries
            ->where('date', '>=', $this->dateStart()->format("Y-m-d"))
            ->where('date', '<=', $this->dateEnd()->format("Y-m-d"))
            ->sum('amount');

        return [
            'employee_price' => $accepted ? $paidTotal / $accepted : 0,
            'deal_lead_conversion' => $deal_lead_conversion,
            'applied_deal_conversion' => $applied_deal_conversion,
            'earned' => $earnedTotalForMonth,
            'paid' => $paidTotalForMonth,
        ];
    }

    protected function described(): array
    {
        return $this->baseQuery()
            ->get()
            ->each(function (User $user) {
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
                $user->referrers_earned = $this->getReferralsEarned($user);
            })->toArray();
    }

    protected function baseQuery(): Builder
    {
        $bindings = [
            $this->dateStart()->format("Y-m-d"),
            $this->dateEnd()->format("Y-m-d"),
        ];

        return User::query()
            ->select(['id', 'referrer_status', 'referrer_id', 'name', 'last_name'])
            ->WhereHas('referralLeads')
            ->withCount(['appliedReferrals as applieds' => fn($query) => $query
                ->whereRelation('description', 'is_trainee', 0)])
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)
                ->where('deal_id', '>', 0)])
            ->withCount(['referralLeads as leads' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)])
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND is_paid = true
                AND date BETWEEN ? AND ?
        ) AS month_paid', $bindings)
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
        ) AS absolute_earned')
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND date BETWEEN ? AND ?
        ) AS month_earned', $bindings)
            ->orderBy('leads', 'desc');
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

    protected function getUserEarned(User $user, ?Carbon $dateStart = null, ?Carbon $dateEnd = null): float
    {
        return $user->referralSalaries()
            ->when($dateStart, fn($query) => $query->whereDate('date', '>=', $dateStart->format("Y-m-d")))
            ->when($dateEnd, fn($query) => $query->whereDate('date', '<=', $dateEnd->format("Y-m-d")))
            ->sum('amount');
    }

    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil((100 * $convertible) / $to);
        }
        return 0;
    }

    protected function getReferralsEarned(User $user): float
    {
        $total = 0;
        /** @var Collection<User> $referrers */
        $referrers = $user->referrals()
            ->whereHas('referralLeads')
            ->get();
        foreach ($referrers as $referrer) {
            $total += $referrer->referralSalaries()
                ->where(function ($query) {
                    $query->where('type', PaidType::FIRST_WORK->name);
                    $query->whereDate('date', $this->dateStart()->format("Y-m-d"));
                    $query->whereDate('date', '<=', $this->dateEnd()->format("Y-m-d"));
                })
                ->sum("amount");
        }
        return $total;
    }
}
