<?php

namespace App\Repositories\Referral;

use App\Service\Referral\Core\LeadTemplate;
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
            'tops' => $this->tops(),
            'referrals' => $this->described(),
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
            ->take(5)
            ->orderBy('applied', 'desc')
            ->get()
            ->toArray();
    }

    protected function baseQuery(): Builder
    {
        /** @var User $user */
        $user = auth()->user();
        return User::query()
            ->where('id', $user->getKey())
            ->WhereHas('referralLeads')
            ->with(['referrals', 'referralSalaries'])
            ->withCount(['referralLeads as deals' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)
                ->where('deal_id', '>', 0)])
            ->withCount(['referralLeads as leads' => fn($query) => $query
                ->where('segment', LeadTemplate::SEGMENT_ID)])
            ->orderBy('leads', 'desc');
    }

    private function getSubReferrers(User $referrer, int $level = 3)
    {
        if ($level === 0) {
            return collect();
        }
        $referrals = $referrer->referrals()
            ->where(function (Builder $query) {
                $query->whereRelation('description', 'is_trainee', 0);
            })
            ->get();

        foreach ($referrals as $referral) {
            $referrals = $referrals->merge($this->getSubReferrers($referral, $level - 1));
        }
        return $referrals;
    }
}