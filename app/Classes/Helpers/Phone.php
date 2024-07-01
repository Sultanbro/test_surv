<?php

namespace App\Classes\Helpers;

/**
 * Вспомогательный класс нормализации телефонных номеров
 */
class Phone
{

    public static function normalize($phone = null)
    {
        if (is_null($phone)) return null;
        if ($phone == '') return null;

        if ($phone[0] == '+') $phone = substr($phone, 1, strlen($phone));
        if ($phone[0] == '8') $phone = '7' . substr($phone, 1, strlen($phone));

        return preg_replace('/[^0-9]/', '', $phone);
    }

    public static function getCountry($phone = null): string
    {

        $country_code = 'UN'; // неизвестный
        if (is_null($phone)) return $country_code;

        $phone = self::normalize($phone);

        if (str_starts_with($phone, '77') && strlen($phone) == 11) $country_code = 'KZ';
        if (str_starts_with($phone, '79') && strlen($phone) == 11) $country_code = 'RU';
        if (str_starts_with($phone, '998') && strlen($phone) == 12) $country_code = 'UZ';
        if (str_starts_with($phone, '996') && strlen($phone) == 12) $country_code = 'KG';
        if (str_starts_with($phone, '375') && strlen($phone) == 12) $country_code = 'BY';
        if (str_starts_with($phone, '380') && strlen($phone) == 12) $country_code = 'UA';
        if (str_starts_with($phone, '374') && strlen($phone) == 11) $country_code = 'AM';
        return $country_code;
    }

    public static function getCountryBitrix($phone = null): int
    {
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

    public static function getCurrency($phone = null): string
    {
        $currencies = [
            'UN' => 'kzt',
            'KZ' => 'kzt',
            'KG' => 'kgs',
            'UZ' => 'uzs',
            'RU' => 'rub',
            'UA' => 'uah',
            'BY' => 'byn',
        ];
        if (is_null($phone)) return 'kzt';
        return $currencies[self::getCountry($phone)];
    }
}
