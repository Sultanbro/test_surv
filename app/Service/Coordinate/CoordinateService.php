<?php
declare(strict_types=1);

namespace App\Service\Coordinate;

use App\DTO\Coordinate\CoordinateDTO;
use App\Models\CentralCoordinate\Coordinate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Matrix\Builder;

/**
* Класс для работы с Service.
*/
class CoordinateService
{
    /**
     * @return Collection
     */
    public function getAllCoordinates():Collection
    {
        return Coordinate::all();
    }

    /**
     * @param CoordinateDTO $dto
     * @return Builder| Model
     */
    public function createCoordinate(CoordinateDTO $dto):Model|BUilder
    {
        return Coordinate::query()->create([
            'country' => $dto->country,
            'city' => $dto->city,
            'geo_lat' => $dto->geo_lat,
            'geo_lon' => $dto->geo_lon,
        ]);
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getCoordinateById($id):Model|null|Builder|Collection|array
    {
        return Coordinate::query()->findOrFail($id);
    }

    /**
     * @param $id
     * @param CoordinateDTO $dto
     * @return array|Collection|Model|Builder|Builder[]|null
     */
    public function updateCoordinate($id, CoordinateDTO $dto):Model|null|Builder|Collection|array
    {
        $coordinate = $this->getCoordinateById($id);
        $coordinate->update([
            'country' => $dto->country,
            'city' => $dto->city,
            'geo_lat' => $dto->geo_lat,
            'geo_lon' => $dto->geo_lon,
        ]);
        return $coordinate;
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteCoordinate($id):void
    {
        $coordinate = $this->getCoordinateById($id);
        $coordinate->delete();
    }
}