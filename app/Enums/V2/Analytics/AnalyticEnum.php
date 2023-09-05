<?php

namespace App\Enums\V2\Analytics;

enum AnalyticEnum
{
    /**
     * Ключ для получения группы.
     */
    const GROUP_KEY = 'groups';

    /**
     * Ключ для получения ТОП.
     */
    const TOP_KEY   = 'tops';

    /**
     * Ключ для получения с кэш строки.
     */
    const ANALYTIC_ROW = 'rows';

    /**
     * Ключ для получения с кэша колонки.
     */
    const ANALYTIC_COLUMN = 'columns';

    /**
     * Ключ для получение с кэша статистику.
     */
    const ANALYTIC_STAT = 'stats';

    /**
     * Ключ для получения активностей.
     */
    const ANALYTIC_ACTIVITIES = 'activities';
}
