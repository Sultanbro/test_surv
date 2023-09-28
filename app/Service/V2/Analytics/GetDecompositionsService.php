<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\Analytics\AnalyticsFacade;

/**
 * Класс для работы с Service.
 */
class GetDecompositionsService
{
    public function handle(GetAnalyticDto $dto): array
    {
        return AnalyticsFacade::decompositionTable($dto);
    }
}