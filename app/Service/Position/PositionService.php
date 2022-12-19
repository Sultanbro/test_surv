<?php
declare(strict_types=1);

namespace App\Service\Position;

use App\Position;
use App\Repositories\PositionRepository;
use Exception;
use Throwable;

/**
* Класс для работы с Service.
*/
final class PositionService
{
    public function __construct(
        public PositionRepository $positionRepository
    )
    {

    }
    /**
     * @Post{
     *  "position": "name"
     * }
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function add(
        string $name
    ): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        try {
            return Position::query()->updateOrCreate(
                ['position' => $name],
                ['position' => $name]
            );
        }catch (\Throwable $exception)
        {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @Delete{
     *  "position": "name"
     * }
     *
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function delete(
        string $name
    )
    {
        try {
            return $this->positionRepository->deleteById($name);
        }catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @Get{
     *  "id": 30
     * }
     *
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function get(
        int $id
    )
    {
        try {
            return $this->positionRepository->getPositionDescriptions($id);
        } catch (\Throwable $exception)
        {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @param string|null $newName
     * @param int|null $indexation
     * @param int|null $sum
     * @param array|null $description
     * @return bool|void
     * @throws Exception
     */
    public function savePositionWithDescription(
        int $id,
        ?string $newName,
        ?int $indexation,
        ?int $sum,
        ?array $description
    )
    {
        try {
             return $this->positionRepository->updatePositionWithDescription($id, $newName, $indexation, $sum, $description);
        } catch (Throwable $exception)
        {
            throw new Exception($exception->getMessage());
        }
    }

    public function allPositions()
    {
        try {
            return $this->positionRepository->allPositionsNameToArray();
        } catch (Throwable $exception)
        {
            throw new Exception($exception->getMessage());
        }
    }
}