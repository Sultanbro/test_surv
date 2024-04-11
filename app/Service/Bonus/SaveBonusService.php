<?php
declare(strict_types=1);

namespace App\Service\Bonus;

use App\Models\Kpi\Bonus;
use App\Traits\KpiHelperTrait;
use Exception;
use Illuminate\Database\QueryException;
use Throwable;

/**
* Класс для работы с Service.
*/
final class SaveBonusService
{
    use KpiHelperTrait;

    /**
     * @param array $bonuses
     * @return bool
     * @throws Exception
     */
    public function handle(
        array $bonuses
    ): bool
    {
        try {

            foreach ($bonuses as $bonus) {
                $bonus['targetable_type'] = $this->getModel($bonus['targetable_type']);
                Bonus::query()->create($bonus);
            }


            return true;
        } catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
