<?php

namespace App\Classes\Helpers;

/**
 * Вспомогательный класс нормализации телефонных номеров
 */

class Phone 
{

    public static function normalize($phone = null) {
        if(is_null($phone)) return null;
        if($phone == '') return null;

        if($phone[0] == '+') $phone = substr($phone, 1, strlen($phone));
        if($phone[0] == '8') $phone = '7' . substr($phone, 1, strlen($phone));

        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        return $phone;
        
    }
    
    public static function getCountry($phone = null) {

        $country_code = 'UN'; // неизвестный
        if(is_null($phone)) return $country_code;

        $phone = self::normalize($phone);

        if(substr($phone, 0, 2) == '77' && strlen($phone) == 11) $country_code = 'KZ';
        if(substr($phone, 0, 2) == '79' && strlen($phone) == 11) $country_code = 'RU';
        if(substr($phone, 0, 3) == '998' && strlen($phone) == 12)  $country_code = 'UZ';
        if(substr($phone, 0, 3) == '996' && strlen($phone) == 12)  $country_code = 'KG';
        if(substr($phone, 0, 3) == '375' && strlen($phone) == 12)  $country_code = 'BY';
        if(substr($phone, 0, 3) == '380' && strlen($phone) == 12)  $country_code = 'UA';
        return $country_code;
    }

    public static function getCountryBitrix($phone = null) {
        $country = [
            'UN' => 0,
            'KZ' => 2346,
            'KG' => 2350,
            'UZ' => 2352,
            'RU' => 2348,
            'UA' => 2392,
            'BY' => 2394,
        ];
        return $country[self::getCountry($phone)];
    }

    public static function getCurrency($phone = null) {
        $currencies = [
            'UN' => 'kzt',
            'KZ' => 'kzt',
            'KG' => 'kgs',
            'UZ' => 'uzs',
            'RU' => 'rub',
            'UA' => 'uah',
            'BY' => 'byn',
        ];
        if(is_null($phone)) return 'kzt';
        return $currencies[self::getCountry($phone)];
    }
}
