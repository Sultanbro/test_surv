<?php

namespace App\Repositories\Referral;

use App\Enums\SalaryResourceType;
use App\Models\Bitrix\Lead;
use App\Salary;
use App\Service\Referral\Core\LeadTemplate;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StatisticRepository implements StatisticRepositoryInterface
{
    protected array $filter = [];

    public function getStatistic(array $filter): array
    {
        $this->filter = $filter;
        return [
            'pivot' => $this->getPivot(),
            'referrers' => $this->getReferrersStatistics()
        ];
    }

    protected function getPivot(): array
    {
        $referrers = User::query()
            ->whereRelation('description', 'applied', '>=', $this->date())
            ->whereHas('referralLeads')
            ->get();

        $accepted = $referrers->where('is_trainee', 0)
            ->whereNotNull('referrer_id')
            ->count();

        $leads = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->count();

        $deals = Lead::query()
            ->where('segment', LeadTemplate::SEGMENT_ID)
            ->whereNot('deal_id', 0)
            ->count();

        $piedTotalForMonth = Salary::query()
            ->where('date', '>=', $this->date())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->where('is_paid', 1)
            ->sum('award');

        $earnedTotalForMonth = Salary::query()
            ->where('date', '>=', $this->date())
            ->where('resource', SalaryResourceType::REFERRAL)
            ->where('is_paid', 0)
            ->sum('award');

        return [
            'employee_price' => $accepted ? $piedTotalForMonth / $accepted : 0,
            'deal_lead_conversion' => $leads ? ($deals / $leads) * 100 : 0,
            'applied_deal_conversion' => $deals ? ($accepted / $deals) * 100 : 0,
            'earned' => $earnedTotalForMonth,
            'paid' => $piedTotalForMonth,
        ];
    }

    protected function getReferrersStatistics(): array
    {
        $userList = is_array($this->usersList()) ? collect($this->usersList()) : $this->usersList();
        return $userList
            ->map(function (User $user) {
                $employees = $user->referrals()
                    ->whereRelation('description', 'is_trainee', 1)
                    ->get();
                $user->deal_lead_conversion_ratio = $this->getRatio($user->deals, $user->leads);
                $user->applieds = count($employees);
                $user->appiled_deal_conversion_ratio = $this->getRatio($user->applieds, $user->deals);
                $user->referrers_earned = $this->getUserReferrersEarned($user, $this->date());
                return $user;
            })
            ->toArray();
    }

    protected function date(): string
    {
        return Carbon::parse($this->filter['date'] ?? now()->format("Y-m-d"))->startOfMonth()->format("Y-m-d");
    }

    protected function getUserEarned(User $user, ?string $date = null): float
    {
        return $user->salaries()
            ->when($date, fn($query) => $query->where('date', '>=', $date))
            ->where('is_paid', 0)
            ->where('resource', SalaryResourceType::REFERRAL)
            ->sum('award');
    }


    protected function getRatio($convertible, $to): float
    {
        if ($to) {
            return ceil(($convertible / $to) * 100);
        }
        return 0;
    }

    protected function getUserReferrersEarned(User $user, ?string $date = null): float
    {
        $total = 0;
        $referrers = $user->referrals()
            ->whereHas('referralLeads')
            ->get();
        foreach ($referrers as $referrer) {
            $total += $this->getUserEarned($referrer, $date);
        }
        return $total;
    }

    protected function usersList(): Collection|array
    {
        return User::query()
            ->WhereHas('referralLeads')
            ->withCount('referralLeads as leads')
            ->withCount(['referralLeads as deals' => fn(Builder $query) => $query
                ->where('deal_id', '>', 0)])
            ->withSum(['salaries as absolute_paid' => fn(Builder $query) => $query
                    ->where('is_paid', 1)
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->withSum(['salaries as absolute_earned' => fn(Builder $query) => $query
                    ->where('is_paid', 0)
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->withSum(['salaries as month_earned' => fn(Builder $query) => $query
                    ->where('is_paid', 0)
                    ->where('date', '>=', $this->date())
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->withSum(['salaries as month_paid' => fn(Builder $query) => $query
                    ->where('is_paid', 1)
                    ->where('date', '>=', $this->date())
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->orderBy('leads', 'desc')
            ->get();
    }
}
