<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    const TIMEZONES = [
        +0 => '(GMT+0:00) Europe/London (Greenwich Mean Time)',
        +1 => '(GMT+1:00) Europe/Belgrade (Central European Time)',
        +2 => '(GMT+2:00) Europe/Minsk (Eastern European Time)',
        +3 => '(GMT+3:00) Europe/Moscow (Moscow Standard Time)',
        +4 => '(GMT+4:00) Europe/Samara (Samara Time)',
        +5 => '(GMT+5:00) Asia/Aqtobe (Aqtobe Time)',
        +6 => '(GMT+6:00) Asia/Almaty (Alma-Ata Time)',
        +7 => '(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)',
        +8 => '(GMT+8:00) Asia/Irkutsk (Irkutsk Time)',
        +9 => '(GMT+9:00) Asia/Yakutsk (Yakutsk Time)',
        +10 => '(GMT+10:00) Asia/Vladivostok (Vladivostok Time)',
        +11 => '(GMT+11:00) Asia/Magadan (Magadan Time)',
        +12 => '(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)'
    ];
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
