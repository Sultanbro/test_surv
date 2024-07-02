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
class AdaptationTalk extends Model
{
    protected $table = 'adaptation_talks';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'day',
        'date', // interview date
        'inter_id',
        'text',
    ];

    /**
     * для страницы настройки профиля
     */
    public static function getTalks($user_id)
    {
        $arr = [];

        $days = [4,15,30,45];
        foreach($days as $day) {

            $item = [];
            $talk = self::where('user_id', $user_id)
                ->where('day', $day)
                ->first();

            if($talk) {
                $item = $talk->toArray();
            } else {
                $item = [
                    'user_id' => $user_id,
                    'day' => $day,
                    'date' => null,
                    'inter_id' => null,
                    'text' => '',
                ];
            }
            
            //$val = strrev(implode(',', str_split(strrev($val), 3)));
            $arr[] = $item;
        }
        
        return $arr;
    }
}
