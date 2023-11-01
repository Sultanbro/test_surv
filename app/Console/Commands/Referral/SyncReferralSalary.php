<?php

namespace App\Console\Commands\Referral;

use App\Enums\SalaryResourceType;
use App\Models\Referral\ReferralSalary;
use App\Salary;
use App\Service\Referral\Core\PaidType;
use Illuminate\Console\Command;

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
        $referrersSalaries = Salary::query()
            ->where('resource', SalaryResourceType::REFERRAL)->get();
        foreach ($referrersSalaries as $referrersSalary) {
            if ($referrersSalary->comment_award) {
                ReferralSalary::query()
                    ->create([
                        'amount' => $referrersSalary->award,
                        'referrer_id' => $referrersSalary->user_id,
                        'referral_id' => (int)$referrersSalary->comment_award,
                        'is_paid' => $referrersSalary->is_paid,
                        'type' => $this->getType($referrersSalary->award),
                    ]);
            }
        }
    }

    private function getType($award): string
    {
        if ($award < 2000) {
            return PaidType::TRAINEE->name;
        }

        if ($award < 10000) {
            return PaidType::ATTESTATION->name;
        }
        return PaidType::FIRST_WORK->name;
    }
}
