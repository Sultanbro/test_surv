<?php

namespace App\Console\Commands\Referral;

use App\Service\Referral\Core\PaidType;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class SyncReferralSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ref-salary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        /** @var Collection<User> $referrers */
        $referrers = User::query()->withWhereHas("referrals")->get();
        foreach ($referrers as $referrer) {
            foreach ($referrer->referrals as $referral) {
                $description = $referral?->description()?->first();
                if (!$description) {
                    break;
                }
                if ($description->is_trainee) {
                    break;
                }
                $referrer->referralSalaries()->firstOrCreate([
                    'referral_id' => $referral->getKey(),
                    'amount' => 5000,
                    'type' => PaidType::ATTESTATION,
                ], [
                    'is_paid' => false,
                    'date' => $referral->created_at->format("Y-m-d"),
                ]);
            }
        }
    }
}
