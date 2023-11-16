<?php

namespace App\Repositories\Referral;

use App\User;
use Illuminate\Database\Eloquent\Builder;

class UserStatisticRepository extends StatisticRepository implements UserStatisticRepositoryInterface
{
    protected array $filter = [];
    private User $user;

    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository
    )
    {
    }

    public function statistic(array $filter, ?User $user = null): array
    {
        $this->filter = $filter;
        $this->user = $user ?? auth()->user();
        return [
            'tops' => $this->tops(),
            'referrals' => $this->described(),
            'mine' => $this->getUserEarned($this->user, $this->dateStart(), $this->dateEnd()),
            'from_referrals' => $this->getReferralsEarned($this->user),
            'absolute' => $this->getUserEarned($this->user),
        ];
    }

    private function tops(): array
    {
        return User::query()
            ->select(['id', 'referrer_status', 'referrer_id', 'name', 'last_name'])
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

    protected function described(): array
    {
        $this->scheduleRepository->setStartDate($this->dateStart());
        $this->scheduleRepository->setStartDate($this->dateEnd());
        $list = parent::described();
        foreach ($list as $item) {
            $item['users'] = $this->scheduleRepository->schedule($this->user);
        }
        return $list;
    }
}