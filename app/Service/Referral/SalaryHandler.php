<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\SalaryCalculatorInterface;
use App\Service\Referral\Core\SalaryHandlerInterface;
use App\Service\Referral\Core\SalaryTransactionInterface;
use App\Service\Referral\Core\TransactionDto;
use App\User;

class SalaryHandler implements SalaryHandlerInterface
{
    public function __construct(
        private readonly SalaryTransactionInterface $transaction,
        private readonly SalaryCalculatorInterface  $calculator
    )
    {
    }

    public function handle(ReferrerInterface $referrer): void
    {
        $calculated = $this->calculator->calculate($referrer);
        foreach ($calculated as $item) {
            /** @var User $referrer */
            $referrer = User::query()->find($item['referrer_id']);
            $dto = new TransactionDto(
                  $referrer
                , $item['expected_salary']
                , now()
                , $referrer->referrer
            );
            $this->transaction->addToTransaction($dto);
        }
        $this->transaction->transfer();
    }
}