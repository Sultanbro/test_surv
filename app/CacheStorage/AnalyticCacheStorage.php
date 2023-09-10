<?php
declare(strict_types=1);

namespace App\CacheStorage;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class AnalyticCacheStorage
{
    private static string $tag = 'AnalyticPage';
    private static int $ttl = 300;

    /**
     * Записывает данные в кэш по ключу.
     *
     * @param string $key
     * @param Collection $data
     * @return bool
     */
    public static function put(
        string $key,
        Collection $data
    ): bool
    {
        return Cache::tags(self::$tag)->put($key, $data, self::$ttl);
    }

    /**
     * Получает данные по ключу.
     *
     * @param string $key
     * @return Collection
     */
    public static function get(
        string $key
    ): Collection
    {
        return Cache::tags(self::$tag)
            ->get($key);
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

    /**
     * @param string $date
     * @param string $key
     * @param ?string $groupId
     * @return string
     */
    public static function key(
        string $date,
        string $key,
        string $groupId = null
    ): string
    {
        return isset($groupId) ? $key . '-' . $groupId . '-' . $date : $key . '-' . $date;
    }
}