<?php

namespace App\CacheStorage;

use App\Events\KpiChangedEvent;
use App\Models\Kpi\Kpi;
use Cache;

class KpiItemsCacheStorage
{
    private static string $tag = 'KpiItems';
    private static int $ttl = 300;
    private static string $prefix = 'kpi-annual-';

    /**
     * Возвращает данные из кэша.
     *
     * @param string $date
     * @return array|null
     */
    public static function get(string $date): array|null
    {
        return json_decode(Cache::tags(self::$tag)
            ->get(self::getKey($date)));
    }

    /**
     * Записывает данные в кэш.
     *
     * @param string $date
     * @param array<Kpi> $kpiItems
     * @return bool
     */
    public static function put(string $date, array $kpiItems): bool
    {
        return Cache::tags(self::$tag)
            ->put(self::getKey($date), json_encode($kpiItems), self::$ttl);
    }

    /**
     * Удаляет данные из кэша по ключу.
     *
     * @param KpiChangedEvent $event <Kpi>
     * @return bool
     */
    public static function onKpiChanged(KpiChangedEvent $event): bool
    {
        $key = self::getKey($event->year . '-' . $event->month);
        return Cache::tags(self::$tag)
            ->forget($key);
    }

    /**
     * Удаляет ВСЕ данные, связанные с KpiItems, из кэша.
     *
     * @return bool
     */
    public static function flush(): bool
    {
        return Cache::tags(self::$tag)
            ->flush();
    }

    /**
     * Возвращает ключ.
     *
     * @param string $date
     * @return string
     */
    private static function getKey(string $date): string
    {
        return self::$prefix . request()->getHost() . '-' . $date;
    }
}
