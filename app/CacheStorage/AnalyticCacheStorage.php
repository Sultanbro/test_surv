<?php
declare(strict_types=1);

namespace App\CacheStorage;

use Illuminate\Support\Facades\Cache;

final class AnalyticCacheStorage
{
    private static string $tag = 'Analytic-Page';
    private static int $ttl = 300;

    /**
     * Записывает данные в кэш по ключу.
     *
     * @param string $key
     * @param array $data
     * @return bool
     */
    public static function put(
        string $key,
        array $data
    ): bool
    {
        return Cache::tags(self::$tag)->put($key, json_encode($data), self::$ttl);
    }

    /**
     * Получает данные по ключу.
     *
     * @param string $key
     * @return array
     */
    public static function get(
        string $key
    ): array
    {
        return json_decode(Cache::tags(self::$tag)
            ->get($key));
    }

    /**
     * Проверяет на наличие данных по ключу.
     *
     * @param string $key
     * @return bool
     */
    public static function has(
        string $key
    ): bool
    {
        return Cache::tags(self::$tag)->has($key);
    }

    /**
     * Удаляет все данные с тэгом Analytic-Page.
     *
     * @return bool
     */
    public static function flush(): bool
    {
        return Cache::tags(self::$tag)->flush();
    }

    /**
     * Удаляет все данные по ключу.
     *
     * @param string $key
     * @return bool
     */
    public static function forgetByKey(
        string $key
    ): bool
    {
        return Cache::tags(self::$tag)->forget($key);
    }
}