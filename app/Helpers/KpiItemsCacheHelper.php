<?php

namespace App\Helpers;

use Cache;

class KpiItemsCacheHelper
{
    private static string $tag = 'KpiItems';
    private static int $ttl = 60;

    /**
     * Возвращает данные из кэша.
     *
     * @param string $key
     * @return array
     */
    public static function get(string $key): array
    {
        return json_decode(Cache::tags(self::$tag)->get($key));
    }

    /**
     * Записывает данные в кэш.
     *
     * @param string $key
     * @param array $value
     * @return bool
     */
    public static function put(string $key, array $value): bool
    {
        return Cache::tags(self::$tag)->put($key, json_encode($value), self::$ttl);
    }

    /**
     * Проверяет данные в кэше по ключу.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return Cache::tags(self::$tag)->has($key);
    }

    /**
     * Удаляет данные из кэша по ключу.
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        return Cache::tags(self::$tag)->forget($key);
    }

    /**
     * Удаляет ВСЕ данные, связанные с KpiItems, из кэша.
     *
     * @return bool
     */
    public static function flush(): bool
    {
        return Cache::tags(self::$tag)->flush();
    }
}
