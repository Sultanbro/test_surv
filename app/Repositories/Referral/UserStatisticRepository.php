<?php

namespace App\Repositories\Referral;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
            'referrals' => $this->forEach(),
            'mine' => $this->getUserEarned($user, $this->date()),
            'from_referrals' => $this->getUserReferrersEarned($user, $this->date()),
            'absolute' => $this->getUserEarned($user),
        ];
    }

    private function tops(User $user): array
    {
        $referrals = $this->getSubReferrers($user->load('referrals'));
        // Sort the sub-referrers by the count of their referrals.
        $sortedSubReferrers = $referrals->sortByDesc(function ($user) {
            return $user->referrals->count();
        });
        return $sortedSubReferrers->take(5)
            ->toArray();
    }

    protected function usersList(): Collection|array
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->referralLeads()->count()) {
            return [];
        }
        return User::query()
            ->where('id', $user->getKey())
            ->withCount('referralLeads as leads')
            ->withCount(['referralLeads as deals' => fn(Builder $query) => $query
                ->where('deal_id', '>', 0)])
            ->withSum('referralSalaries as absolute_paid', 'amount')
            ->withSum(['referralSalaries as month_paid' => fn(Builder $query) => $query
                ->where('date', '>=', $this->date())
            ], 'amount')
            ->get();
    }


    private function getSubReferrers(User $user, int $level = 3)
    {
        if ($level === 0) {
            return collect();
        }
        $referrals = $user->referrals()
            ->with([
                'referrals' => fn(Builder $query) => $query->whereHas('referrals')
                    ->whereRelation('description', 'applied', '>=', $this->date())
            ])
            ->whereHas('referralLeads')
            ->get();

        foreach ($referrals as $referral) {
            $referrals = $referrals->merge($this->getSubReferrers($referral->load('referrals'), $level - 1));
        }
        return $referrals;
    }
}
