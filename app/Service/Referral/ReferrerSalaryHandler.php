<?php

namespace App\Service\Referral;

use App\Models\Referral\Referrer;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\ReferrerSalaryCalculatorInterface;
use App\Service\Referral\Core\ReferrerSalaryHandlerInterface;
use App\Service\Referral\Core\ReferrerSalaryTransactionInterface;
use App\Service\Referral\Core\TransactionDto;

class ReferrerSalaryHandler implements ReferrerSalaryHandlerInterface
{
    public function __construct(
        private readonly ReferrerSalaryTransactionInterface $transaction,
        private readonly ReferrerSalaryCalculatorInterface  $calculator
    )
    {
    }

    public function handle(ReferrerInterface $referrer): void
    {
        $calculated = $this->calculator->calculate($referrer);
        foreach ($calculated as $item) {
            /** @var Referrer $referrer */
            $referrer = Referrer::with('user')->find($item['referrer_id']);
            $dto = new TransactionDto(
                  $referrer->user
                , $item['expected_salary']
                , now()
            );
            $this->transaction->addToTransaction($dto);
        }
        $this->transaction->transfer();
    }
}