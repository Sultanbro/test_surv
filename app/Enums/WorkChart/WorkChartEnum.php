<?php
declare(strict_types=1);

namespace App\Enums\WorkChart;

enum WorkChartEnum
{
    /**
     * Max count of day per week for each type can work.
     */
    const COUNT_OF_WORK_DAY_FOR_TWO_BY_TWO  = 4;
    const COUNT_OF_WORK_DAY_FOR_THREE_BY_THREE = 4;
    const COUNT_OF_WORK_DAY_FOR_ONE_BY_ONE  = 4;
    const COUNT_OF_WORK_DAY_FOR_FIVE_BY_TWO = 5;
    const COUNT_OF_WORK_DAY_FOR_SIX_BY_ONE  = 6;

    /**
     * Types of work chart for groups and users
     */
    const FIVE_BY_TWO = '5-2';
    const SIX_BY_ONE = '6-1';
    const THREE_BY_THREE = '3-3';
    const TWO_BY_TWO = '2-2';
    const ONE_BY_ONE = '1-1';
}
