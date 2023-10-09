<?php

namespace App\Service\Referral\Core;

use App\Enums\SalaryType;
use App\Exceptions\TransactionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReferrerSalaryTransaction implements ReferrerSalaryTransactionInterface
{
    protected array $transaction = [];

    public function addToTransaction(TransactionDto $dto): static
    {
        $this->transaction[] = $dto;
        return $this;
    }

    /**
     * @throws Throwable
     */
    public function transfer(): void
    {
        DB::transaction(function () {
            try {
                /** @var TransactionDto $item */
                foreach ($this->transaction as $item) {
                    $item->user->salaries()->create([
                        'date' => now()->format('Y-m-d'),
                        'amount' => 0,
                        'type' => SalaryType::BONUS,
                        'comment_bonus' => "Oт реферальной ссылки",
                        'bonus' => $item->salary,
                    ]);
                }
                DB::commit();
            } catch (TransactionException $e) {
                DB::rollBack();
                Log::error($e->getMessage());
            }
        }, 2);
    }

    public function getCurrentTransaction(): array
    {
        return $this->transaction;
    }
}