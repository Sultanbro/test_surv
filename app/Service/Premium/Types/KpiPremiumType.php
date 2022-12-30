<?php
declare(strict_types=1);

namespace App\Service\Premium\Types;

use App\Repositories\EditedKpiRepository;
use App\Service\Interfaces\Premium\PremiumTypeInterface;

class KpiPremiumType implements PremiumTypeInterface
{
    private int $userId;
    private string $amount;
    private string $comment;
    private string $date;
    private $repository;

    public function __construct(
        int $userId,
        string $amount,
        string $comment,
        string $date
    )
    {

        $this->userId = $userId;
        $this->amount = $amount;
        $this->comment = $comment;
        $this->date = $date;
        $this->repository = new EditedKpiRepository();
    }

    public function executeType()
    {
        $exist = $this->repository->getUserEditedKpiPerDate($this->userId, $this->date);

        if (!$exist)
        {
            $this->repository->createEditedKpi($this->userId, $this->amount, $this->comment, $this->date);
        }
    }
}