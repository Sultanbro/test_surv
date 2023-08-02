<?php

namespace App\Repositories\Coordinate;

use App\Models\CentralCoordinate\Coordinate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CoordinateRepository
{
    /**
     * @return Collection
     */
    public function all():Collection
    {
        return Coordinate::all();
    }

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function create(array $data):Builder|Model
    {
        return Coordinate::query()->create($data);
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function find($id):Builder|array|Collection|Model|null
    {
        return Coordinate::query()->findOrFail($id);
    }

    /**
     * @param $id
     * @param array $data
     * @return array|Builder|Builder[]|Collection|Model|null
     */
    public function update($id, array $data):Builder|array|Collection|Model|null
    {
        $coordinate = $this->find($id);
        $coordinate->update($data);
        return $coordinate;
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id):void
    {
        $coordinate = $this->find($id);
        $coordinate->delete();
    }
}