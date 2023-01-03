<?php
declare(strict_types=1);

namespace App\Service\Premium\Types;

use App\Repositories\EditedBonusRepository;
use App\Repositories\EditedSalaryRepository;
use App\Service\Interfaces\Premium\PremiumTypeInterface;
use Symfony\Component\HttpFoundation\Response;

final class FinalPremiumType implements PremiumTypeInterface
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
        $this->repository = new EditedSalaryRepository();
    }

    /**
     * @return int
     */
    public function executeType(): int
    {
        $this->repository->updateOrCreate($this->userId, $this->amount, $this->comment, $this->date);

        return Response::HTTP_CREATED;
    }
}