<?php

namespace App\Repositories\Referral;

use App\User;
use Illuminate\Database\Eloquent\Builder;

class UserStatisticRepository extends StatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];

    public function statistic(array $filter, ?User $user = null): array
    {
        $this->filter = $filter;
        /** @var User $user */
        $user = $user ?? auth()->user();
        $user->load(['referralSalaries']);
        return [
            'tops' => $this->tops(),
            'referrals' => $this->described(true),
            'mine' => $this->getUserEarned($user, $this->dateStart(), $this->dateEnd()),
            'from_referrals' => $this->getReferralsEarned($user),
            'absolute' => $this->getUserEarned($user),
        ];
    }

    private function tops(): array
    {
        return User::query()
            ->select(['id', 'name', 'last_name', 'referrer_status', 'img_url'])
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

    protected function baseQuery(): Builder
    {
        /** @var User $user */
        $user = auth()->user();
        $query = parent::baseQuery();
        $query->where('id', $user->getKey());
        return $query;
    }
}