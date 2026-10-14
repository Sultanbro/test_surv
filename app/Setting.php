<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int value
 */
class Setting extends Model
{
    const TIMEZONES = [
        +0 => 'Europe/London',
        +1 => 'Europe/Belgrade',
        +2 => 'Europe/Minsk',
        +3 => 'Europe/Moscow',
        +4 => 'Europe/Samara',
        +5 => 'Asia/Aqtobe',
        +6 => 'Asia/Almaty',
        +7 => 'Asia/Krasnoyarsk',
        +8 => 'Asia/Irkutsk',
        +9 => 'Asia/Yakutsk',
        +10 => 'Asia/Vladivostok',
        +11 => 'Asia/Magadan',
        +12 => 'Asia/Kamchatka'
    ];

    const DEFAULT_MANAGER = "default_manager";

    protected $fillable = ['name', 'description', 'value'];

    public static function get($name, $currency, $number = null) {

        if(!is_null($number)) {
            if(starts_with($number, 77) || starts_with($number, 79)) {
                $number = substr_replace($number,'8',0,1);
            }

            if(starts_with($name, 'autocall')) {
                $tarrifs = Tarrif::all();
            } else {
                $tarrifs = MessageTarrif::all();
            }

            $codes = $tarrifs->filter(function ($value, $key) use ($number) {
                return starts_with($number, $value->prefix);
            });

            if(!$codes->isEmpty()) {
                $code = $codes->sortByDesc('prefix')->first();
                if($name == 'autocall_bill_duration' || $name == 'autocall_transfer_bill_duration')
                    return $code->$name;
                else
                    return floatval($currency=='kzt'?$code->$name[0]:$code->$name[1]);
            }
        }

        $setting = Setting::where('name', $name.'_'.$currency)->first();

        if($setting)
            return floatval($setting->value);

        return false;
    }
}
