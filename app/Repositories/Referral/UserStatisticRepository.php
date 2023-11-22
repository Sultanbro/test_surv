<?php

namespace App\Repositories\Referral;

use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Scheduler;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserStatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];
    private User $user;
    /**
     * @var true
     */
    private bool $onlyReferralsStatistics = false;

    public function __construct(
        private readonly Scheduler $scheduler
    )
    {
    }

    public function statistic(array $filter, ?User $user = null): array
    {
        if ($user) $this->onlyReferralsStatistics = true;

        $this->filter = $filter;
        /** @var User $user */
        $this->user = $this->baseQuery($user ?? auth()->user());
        $this->scheduler->setFilter($filter);
        $this->scheduler->schedule($this->user);

        $return = [
            'mine' => $this->user->month_earned,
            'from_referrals' => $this->user->from_referrals,
            'absolute' => $this->user->absolute_earned,
            'referrals' => $this->user->referrals,
        ];

        if (!$this->onlyReferralsStatistics) {
            $return['tops'] = $this->tops();

        }

        return $return;
    }

    private function tops(): array
    {
        return User::query()
            ->select(['id', 'name', 'last_name', 'referrer_status', 'img_url'])
            ->whereHas('referrals')
            ->withCount(['referrals as applied_count' => function ($query) {
                $query->whereRelation('description', 'is_trainee', 0);
            }])
            ->groupBy('users.id')
            ->having('applied_count', '>', 0)
            ->take(5)
            ->orderBy('applied_count', 'desc')
            ->get()
            ->toArray();
    }

    protected function baseQuery(User $user): User
    {
        $bindings = [
            $this->dateStart()->format("Y-m-d"),
            $this->dateEnd()->format("Y-m-d"),
        ];

        /** @var User */
        return User::query()
            ->where('id', $user->getKey())
            ->select(['id', 'name', 'referrer_status', 'referrer_id', 'deleted_at'])
            ->withCount(['appliedReferrals as applieds' => fn($query) => $query
                ->whereRelation('description', 'is_trainee', 0)])
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)
                ->where('deal_id', '>', 0)])
            ->withCount(['referralLeads as leads' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)])
            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
            ->with(['referralSalaries'])
            ->with(['referrals' => function (HasMany $query) {
                $query->select(['id', 'name', 'referrer_status', 'referrer_id', 'deleted_at'])
                    ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
                    ->with(['referrals' => function (HasMany $query) {
                        $query->select(['id', 'name', 'referrer_status', 'referrer_id', 'deleted_at'])
                            ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])])
                            ->with(['referrals' => function (HasMany $query) {
                                $query->select(['id', 'name', 'referrer_status', 'referrer_id', 'deleted_at'])
                                    ->with(['user_description' => fn($query) => $query->select(['id', 'user_id', 'is_trainee'])]);
                            }]);
                    }])
                    ->with(['daytypes' => function (HasMany $query) {
                        $query->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                            ->whereMonth('date', '=', $this->dateStart()->month)
                            ->whereYear('date', $this->dateStart()->year);
                    }]);
            }])
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
            ->selectRaw('(
            SELECT SUM(amount)
            FROM referral_salaries
            WHERE users.id = referral_salaries.referrer_id
                AND date BETWEEN ? AND ?
                AND type != ? 
        ) AS from_referrals', [
                ...$bindings,
                PaidType::WORK->name
            ])
            ->first();
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
}