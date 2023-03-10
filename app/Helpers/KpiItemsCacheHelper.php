<?php

namespace App\Helpers;

use Cache;
use App\Models\Kpi\Kpi;
use \App\Events\KpiChangedEvent;

class KpiItemsCacheHelper
{
    private static string $tag = 'KpiItems';
    private static int $ttl = 60;
    private static string $prefix = 'kpi-annual-';

    /**
     * Возвращает данные из кэша.
     *
     * @param string $date
     * @return array|null
     */
    public static function get(string $date): array|null
    {
        return json_decode(Cache::tags(self::$tag)->get(self::$prefix . $date));
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
            ->put(self::$prefix . $date, json_encode($kpiItems), self::$ttl);
    }

    /**
     * Удаляет данные из кэша по ключу.
     *
     * @param KpiChangedEvent $event <Kpi>
     * @return bool
     */
    public static function onKpiChanged(KpiChangedEvent $event): bool
    {
        $key = self::$prefix . $event->year . '-' .  $event->month;
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
