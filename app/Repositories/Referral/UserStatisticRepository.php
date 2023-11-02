<?php

namespace App\Repositories\Referral;

use App\User;
use Illuminate\Database\Eloquent\Builder;

class UserStatisticRepository extends StatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];

    public function statistic(array $filter): array
    {
        $this->filter = $filter;
        /** @var User $user */
        $user = auth()->user();
        return [
            'tops' => $this->tops($user),
            'referrals' => $this->described(),
            'mine' => $this->getUserEarned($user, $this->date()),
            'from_referrals' => $this->getUserReferralEarned($user, $this->date()),
            'absolute' => $this->getUserEarned($user),
        ];
    }

    private function tops(User $user): array
    {
        $tree = $this->getSubReferrers($user);
        // Sort the sub-referrers by the count of their referrals.
        return $tree->sortByDesc(fn($user) => $user->referrals->count())
            ->take(5)
            ->toArray();
    }

    protected function baseQuery(): Builder
    {
        /** @var User $user */
        $user = auth()->user();
        return User::query()
            ->where('id', $user->getKey())
            ->withCount('referralLeads as leads')
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('deal_id', '>', 0)])
            ->withSum('referralSalaries as absolute_paid', 'amount')
            ->withSum(['referralSalaries as month_paid' => fn($query) => $query
                ->where('date', '>=', $this->date()->format("Y-m-d"))
            ], 'amount');
    }

    private function getSubReferrers(User $referrer, int $level = 3)
    {
        if ($level === 0) {
            return collect();
        }
        $referrals = $referrer->referrals()
            ->where(function ($query) {
                $query->whereRelation('description', 'is_trainee', 0);
            })
            ->get();

        foreach ($referrals as $referral) {
            $referrals = $referrals->merge($this->getSubReferrers($referral, $level - 1));
        }
        return $referrals;
    }
}