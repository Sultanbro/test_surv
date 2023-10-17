<?php

namespace App\Repositories\Referral;

use App\Enums\SalaryResourceType;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserStatisticRepository extends StatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];

    public function getStatistic(array $filter): array
    {
        $this->filter = $filter;
        /** @var User $user */
        $user = auth()->user();
        return [
            'tops' => $this->tops($user),
            'referrals' => $this->getReferrersStatistics(),
            'mine' => $this->getUserEarned($user, $this->date()),
            'from_referrals' => $this->getUserReferrersEarned($user, $this->date()),
            'absolute' => $this->getUserAbsoluteEarned($user),
        ];
    }

    private function tops(User $user): array
    {
        return $this->getAllReferrers($user);
    }

    protected function usersList(): Collection|array
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->referralLeads()->count()) {
            return [];
        }
        return $user
            ->loadCount('referralLeads as leads')
            ->loadCount(['referralLeads as deals' => fn(Builder $query) => $query
                ->where('deal_id', '>', 0)])
            ->loadSum(['salaries as absolute_paid' => fn(Builder $query) => $query
                    ->where('resource', SalaryResourceType::REFERRAL)]
                , 'award')
            ->get();
    }

    private function getAllReferrers(Authenticatable|User $user)
    {
        $subReferrers = $this->getSubReferrers($user->load('referrals'));
        // Sort the sub-referrers by the count of their referrals.
        $sortedSubReferrers = $subReferrers->sortByDesc(function ($user) {
            return $user->referrals->count();
        });
        return $sortedSubReferrers->take(5);
    }

    private function getSubReferrers(User $user, int $level = 3)
    {
        if ($level === 0) {
            return collect();
        }
        $subReferrers = $user->referrals;

        foreach ($subReferrers as $subReferrer) {
            $subReferrers = $subReferrers->merge($this->getSubReferrers($subReferrer->load('referrals'), $level - 1));
        }
        return $subReferrers;
    }
}
