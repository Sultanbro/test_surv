<?php

namespace App\Repositories;

use App\Models\Tax;
use App\Models\Tax as Model;
use Illuminate\Support\Facades\DB;

/**
 * Шаблон Repository для налогов.
 */
class TaxRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getArray()
    {
        return Tax::query()->get()->toArray();
    }
    /**
     * Создать новый налог для сотрудника.
     * @param array $data
     * @return void
     */
    public function createNewTax(array $data): void
    {
        $this->model()->create($data);
    }

    /**
     * Создать несколько новых налогов для сотрудника.
     * @param array $data
     * @return void
     */
    public function insertMultipleTaxes(array $data): void
    {
        $this->model()->insert(array_map(function ($tax) {
            $tax['created_at'] = now();
            $tax['updated_at'] = now();

            return $tax;
        }, $data));
    }

    /**
     * @param $id
     * @param array $ids
     * @param array $data
     * @return void
     */
    public function updateOrDelete($id, array $ids, array $data): void
    {
        $this->model()
            ->where('user_id', $id)
            ->whereNotIn('id', $ids)
            ->delete();

        foreach ($data as $id => $record) {
            $this->model()
                ->where('id', $id)
                ->update($record);
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteAll($id): void
    {
        $this->model()->where('user_id', $id)->delete();
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserTaxes(
        int $userId
    ): array
    {
        return DB::table('user_tax')->where('user_id', $userId) ->get()->toArray();
    }
}
