<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;

    const COLUMN_PENALTY_AMOUNT = 'penalty_amount';
    const TYPE_LATE_MORE_5 = 1; // больше 5 минут опоздание
    const TYPE_LATE_LESS_5 = 2; // меньше 5 минут опоздание

    const STATUS_FIRST = 1;
    const STATUS_SECOND = 2;

    /**
     * Добавление нового штрафа
     *
     * @param array $data
     * @return integer
     */
    public function addFine(array $data)
    {
        $fine = new Fine;
        $fine->name = $data['name'];
        $fine->penalty_amount = $data[self::COLUMN_PENALTY_AMOUNT];
        $fine->save();

        return $fine->id;
    }

    /**
     * Редактирование штрафа
     *
     * @param array $data
     * @return integer
     */
    public function editFine(array $data)
    {
        $updateData['name'] = $data['name'];
        $updateData[self::COLUMN_PENALTY_AMOUNT] = $data[self::COLUMN_PENALTY_AMOUNT];

        return Fine::where('id', '=', $data['id'])->update($updateData);
    }

    /**
     * Удаление штрафа
     *
     * @param $id
     * @return integer
     */
    public function deleteFine($id)
    {
        return Fine::where('id', '=', $id)->delete();
    }
}
