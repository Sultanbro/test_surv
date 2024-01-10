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
        return Tax::query()
            ->select('taxes.id', 'taxes.name', 'user_tax.is_percent', 'taxes.end_subtraction',
                DB::raw('CASE WHEN user_tax.value > 0 THEN user_tax.value ELSE taxes.value END AS value'),
                DB::raw('(user_tax.user_id IS NOT NULL) as isAssigned'))
            ->join('user_tax', function ($join) use ($userId) {
                $join->on('user_tax.tax_id', '=', 'taxes.id')
                    ->where('user_tax.user_id', '=', $userId);
            })
            ->get()
            ->toArray();
    }
}
