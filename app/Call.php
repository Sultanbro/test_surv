<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $connection = 'callibro';
    protected $table = 'calls';

    public $timestamps = true;

    const CALLBACK_NONE = 0;
    const CALLBACK_BY_POST_REQUESET = 1;
    const CALLBACK_BY_RECOVERY_CRON = 2;
    const CALLBACK_BY_INCOME_CRON = 3;
    const CALLBACK_BY_DIRECT_CRON = 4;

    const AMOUNT_BY_CRON = 0.8;
    const AMOUNT_BY_CRON_RU = 0.13;
    const EXPENSE_TYPE = 'callibro';

    const RENT_AMOUNT_BY_CRON = 15;
    const RENT_AMOUNT_BY_CRON_RU = 2.5;
    const RENT_EXPENSE_TYPE = 'rent_number';

    const TYPE_CALL_OUT = 0;
    const TYPE_CALL_INCOME = 1;
    const TYPE_CALL_DIRECT = 2;


    const IS_CUMPLITED = 1;
    const IS_IN_QUEUE = 0;
    const IS_ERROR_FINISHED = -1;

    protected $fillable = [
        'call_contact_id',
        'call_dialer_id',
        'call_account_id',
        'operator_time',
        'script_status_id',
        'script_status_title',
        'start_time',
        //'call_time',
        'number',
        'duration',
        'billsec',
        'opersec',
        'is_success',
        'is_callback',
        'is_income',
        'session_id',
        'behovior',
        'correct_or_not'
    ];


    public static $statuses = array(
        'NORMAL_TEMPORARY_FAILURE' => 'Успешно',
        'NORMAL_CLEARING' => 'Успешно',
        'RECOVERY_ON_TIMER_EXPIRE' => 'Успешно',
        'NO_ANSWER' => 'Абонент не ответил',
        'USER_BUSY' => 'Абонент занят',
        'NO_ROUTE_DESTINATION' => 'Отключен',
        'ALLOTTED_TIMEOUT' => 'Абонент не ответил',
        'MANAGER_REQUEST' => 'Успешно',
        'CALL_REJECT' => 'Отклонено'
    );

    public function contact()
    {
        return $this->belongsTo('App\Obzvon\Contact', 'call_contact_id', 'id');
    }
    public function dialer()
    {
        return $this->belongsTo('App\Obzvon\Dialer', 'call_dialer_id', 'id');
    }

    public function grades()
    {
        return $this->hasMany('App\CallGrade', 'call_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo('App\Obzvon\Account', 'call_account_id', 'id');
    }

    public static function text( $cause ) {
        $title = $cause;

        switch ( $cause ) {
            case 'NORMAL_CLEARING':
                $title = 'SIP 200 ОК. Успешное соединение.';
                break;
            case 'NO_ANSWER':
                $title = 'SIP 180 No answer. Нет ответа. Вызываемая сторона не ответила за время вызова.';
                break;
            case 'USER_BUSY':
                $title = 'SIP 486 Busy. Абонент занят.';
                break;
            case 'NORMAL_TEMPORARY_FAILURE':
                $title = 'SIP 503 Internal error. Вызов был отклонен вышим провайдером связи.';
                break;
            case 'NO_ROUTE_DESTINATION':
                $title = 'SIP 404 Not found. Номер абонента не существует. Возможно номер не верный, но возможно, оператор связи заблокировал исходящие звонки с вашего номера.';
                break;
        }

        return $title;
    }



}
