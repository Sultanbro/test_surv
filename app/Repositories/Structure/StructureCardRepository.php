<?php

namespace App\Repositories\Structure;

use App\Models\Structure\StructureCard;
use App\Repositories\CoreRepository;
use Illuminate\Database\Eloquent\Model;

/**
* Класс для работы с Repository.
*/
class StructureCardRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass():string
    {
        return StructureCard::class;
    }

    /**
     * @param array $data
     * @return  Model
     */
    public function create(array $data):Model
    {
        return $this->model()->query()->create($data);
    }

}