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
        return [
            'tops' => $this->tops(),
            'referrals' => $this->table(true),
            'mine' => $this->getUserEarned($user, $this->dateStart(), $this->dateEnd()),
            'from_referrals' => $this->getReferralsEarned($user),
            'absolute' => $this->getUserEarned($user),
        ];
    }

    private function tops(): array
    {
        return User::query()
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