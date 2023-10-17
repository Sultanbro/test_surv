<?php

namespace App\Service\Referral\Core;

interface SalaryTransactionInterface
{
    public function addToTransaction(TransactionDto $dto): static;

    public function transfer(): void;
    public function getCurrentTransaction(): array;
}