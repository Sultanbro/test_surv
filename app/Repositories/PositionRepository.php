<?php

namespace App\Repositories;

use App\Position;
use App\Position as Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Класс для работы с Repository.
 */
class PositionRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPositionIdNameWithPluck(): array
    {
        return $this->model()->get()->pluck('position','id')->toArray();
    }

    /**
     * @return array
     */
    public function getPositionIdName(): array
    {
        return $this->model()->select('position as name', 'id')->get()->toArray();
    }

    /**
     * @param string $positionName
     * @return mixed
     */
    public function createPosition(string $positionName)
    {
        return $this->model()->create([
            'position' => $positionName
        ]);
    }

    /**
     * @param int $positionId
     * @return mixed
     */
    public function deletePosition(int $positionId)
    {
        return $this->model()->findOrFail($positionId)->delete();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById(
        int $id
    ): bool
    {
        return $this->model()->where('id', $id)->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getPositionDescriptions(
        int $id
    ): mixed
    {
        return $this->model()->join('position_descriptions as pd', 'pd.position_id', '=', 'position.id')->where('position.id', $id)->get();
    }

    /**
     * @param int $id
     * @param string|null $newName
     * @param int|null $indexation
     * @param int|null $sum
     * @param array|null $description
     * @param bool|null $is_head
     * @param bool|null $is_spec
     * @param bool|null $ckp_status
     * @param string|null $ckp
     * @param string|null $ckp_link
     * @return bool
     */
    public function updatePositionWithDescription(
        int $id,
        ?string $newName,
        ?int $indexation,
        ?int $sum,
        ?array $description,
        ?bool $is_head,
        ?bool $is_spec,
        ?bool $ckp_status,
        ?string $ckp,
        ?string $ckp_link,
    )
    {
         $positionUpdated = $this->model()->findOrFail($id)->update([
             'position' => $newName,
             'indexation' => $indexation,
             'sum' => $sum,
             'is_head' => $is_head,
             'is_spec' => $is_spec,
             'ckp_status' => $ckp_status,
             'ckp' => $ckp,
             'ckp_link' => $ckp_link,
         ]);

         if ($positionUpdated && isset($description))
         {
             $this->model()->findOrFail($id)->descriptions()->updateOrCreate(
                 [
                     'position_id' => $id
                 ],
                 [
                     'require'  => $description['require'],
                     'actions'  =>$description['actions'],
                     'time'     => $description['time'],
                     'salary'   => $description['salary'],
                     'knowledge' => $description['knowledge'],
                     'next_step' => $description['next_step'],
                     'show' => $description['show'],
                 ]
             );
         }

         return true;
    }

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function allPositionsNameToArray()
    {
        return $this->model()->get()->pluck('position')->toArray();
    }
}
