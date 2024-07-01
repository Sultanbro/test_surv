<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use App\Models\Analytics\UserStat;

class RecruitingActivityService
{

    /**
     * Ids of HR activities
     * only for Bpartners
     */
    public $activities = [
        22, // Наборы
        45, // Диалоги от 10с
        49, // Время первого звонка
        50, // Время последнего звонка
        51, // Входящие
        52, // Пропущенные
        53, // Сделки
        54, // Принято
        204, // 1 день стажировавшихся
        207, // 2+ день стажировавшихся
    ];

    /**
     * user_id
     */
    public int $user_id;

    /**
     * date
     */
    public String $date;

    /**
     * Поля индивидуальной таблицы c Recruting
     */
    CONST I_CALL_PLAN = 0; // План наборов с ожиданием 7 гудков
    CONST I_CALLS_OUT = 1; // Успешные исходящие
    CONST I_FIRST_CALL = 2; // Время первого звонка
    CONST I_LAST_CALL = 3; // Время последнего звонка
    CONST I_CALLS_IN = 4; // Обработано успешных входящих
    CONST I_CALLS_MISSED = 5; // Пропущенные звонки
    CONST I_CONVERTED = 6; // Сконвертировано
    CONST I_APPLIED = 7; // Принято на работу
    CONST I_FIRST_DAY_TRAINED = 8; // 1 день стажировавшихся
    CONST I_SECOND_DAY_TRAINED_FROM = 9; // 2+ день стажировавшихся

    /**
     * Сохранить
     * @param $request
     * @return void
     */
    public function save(int $row, mixed $value) : void
    {
        $us = UserStat::where('activity_id', $this->activities[$row])
                    ->where('user_id', $this->user_id)
                    ->where('date', $this->date)
                    ->first();

        if($us) {
            $us->value = (float) $value;
            $us->save();
        } else {
            UserStat::create([
                'user_id'      => $this->user_id,
                'date'         => $this->date,
                'value'        => (float) $value,
                'activity_id'  => $this->activities[$row],
            ]);
        }
    }

    /**
     * setDate
     */
    public function setDate(String $date) 
    {
        $this->date = $date;
    }

    /**
     * setDate
     */
    public function setUser(int $user_id) 
    {
        $this->user_id = $user_id;
    }

    /**
     * getUser
     */
    public function getUser() 
    {
        return $this->user_id;
    }
}