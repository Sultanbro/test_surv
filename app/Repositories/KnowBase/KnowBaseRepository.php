<?php
declare(strict_types=1);

namespace App\Repositories\KnowBase;

use App\KnowBase as Model;
use App\Repositories\CoreRepository;

/**
* Класс для работы с Repository.
*/
final class KnowBaseRepository extends CoreRepository
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
     * @param array $ids
     * @return mixed
     */
    public function getCorpBooks(
        array $ids
    )
    {
        return $this->model()->whereIn('id', $ids)->get();
    }
}