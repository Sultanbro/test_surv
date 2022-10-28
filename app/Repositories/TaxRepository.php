<?php

namespace App\Repositories;

use App\Models\Tax as Model;

/**
 * Шаблон Repository для налогов.
 */
class TaxRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
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
     * @param array $ids
     * @param array $data
     * @return void
     */
    public function updateOrDelete(array $ids, array $data): void
    {
        $this->model()->whereNotIn('id', $ids)->delete();

        foreach ($data as $id => $record)
        {
            $this->model()->where('id', $id)->update($record);
        }
    }
}