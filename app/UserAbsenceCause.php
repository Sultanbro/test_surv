<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
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
    const FIRST_DAY = 1; // Пропал с обучения: 2 день
    const SECOND_DAY = 2; // Пропал с обучения: 2 день
    const THIRD_DAY = 3; // Пропал с обучения
    const FIRED = 4;

    public static function createOrUpdate($data)
    {
        $date = $data['date'];
        $user_id = $data['user_id'];
        $type = $data['type'];
        $text = $data['text'];

        $us = self::where('user_id', $user_id)->where('date', $date)->where('type', $type)->first();
        if ($us) {
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

    public static function absenceCauseByType($list, $type): array
    {
        $result = [];
        $items = $list->where('type', $type);
        dd($items);
        foreach ($items as $key => $th) {
            $result[] = [
                'cause' => $key,
                'count' => $th->count,
            ];
        }

        $DESC = array_column($result, 'count');
        array_multisort($DESC, SORT_DESC, $result);
        return $result;
    }

}