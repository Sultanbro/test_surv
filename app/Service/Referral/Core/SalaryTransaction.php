<?php

namespace App\Service\Referral\Core;

use App\Enums\SalaryResourceType;
use App\Exceptions\TransactionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SalaryTransaction implements SalaryTransactionInterface
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
                    $referrerName = $item->parent ? $item->parent->name . ' ' . $item->parent->last_name : '';
                    $item->user->salaries()->create([
                        'date' => now()->format('Y-m-d'),
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => "Oт реферальной ссылки " . $referrerName,
                        'award' => $item->salary,
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