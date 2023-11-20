<?php

namespace App\Repositories\Referral;

use App\User;
use Illuminate\Database\Eloquent\Builder;

class UserStatisticRepository extends StatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];
    private User $user;

    public function statistic(array $filter, ?User $user = null): array
    {
        $this->filter = $filter;
        /** @var User $user */
        $this->user = $user ?? auth()->user();
        if($user) return [
            'referrals' => $this->described(true),
        ];
        return [
            'tops' => $this->tops(),
            'referrals' => $this->described(true),
            'mine' => $this->getUserEarned($this->user, $this->dateStart(), $this->dateEnd()),
            'from_referrals' => $this->getReferralsEarned($this->user),
            'absolute' => $this->getUserEarned($this->user),
        ];
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

    protected function baseQuery(): Builder
    {
        $query = parent::baseQuery();
        $query->where('id', $this->user->getKey());
        return $query;
    }
}