<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\User;
use App\ProfileGroup;

/**@brief test
 * 
 */
class UserAbsenceCause extends Model
{
    protected $table = 'user_absence_causes';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'text',
        'date',
    ];

    // type
    CONST FIRST_DAY = 1; // Пропал с обучения: 2 день
    CONST SECOND_DAY = 2; // Пропал с обучения: 2 день
    CONST THIRD_DAY = 3; // Пропал с обучения
    CONST FIRED = 4;

    public static function createOrUpdate($data)
    {
        $date = $data['date'];
        $user_id = $data['user_id'];
        $type = $data['type'];
        $text = $data['text'];

        $us = self::where('user_id', $user_id)->where('date', $date)->where('type', $type)->first();
        if($us) {
            $us->text = $text;
            $us->date = $date;
            $us->save();
        } else {
            self::create([
                'user_id' => $user_id,
                'type' => $type,
                'date' => $date,
                'text' => $text
            ]);
        }
    }

}