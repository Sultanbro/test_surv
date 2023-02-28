<?php
declare(strict_types=1);

namespace App\Service\WorkChart;

use App\Service\WorkChart\Types\FiveByTwoChart;
use App\Service\WorkChart\Types\OneByOneChart;
use App\Service\WorkChart\Types\SixByOneChart;
use App\Service\WorkChart\Types\ThreeByThreeChart;
use App\Service\WorkChart\Types\TwoByTwoChart;
use InvalidArgumentException;

final class ChartFactory
{
    /**
     * @param string $chartType
     * @return Chart
     */
    public static function make(string $chartType): Chart
    {
        return match ($chartType) {
            "1-1"   => new OneByOneChart(),
            "2-2"   => new TwoByTwoChart(),
            "3-3"   => new ThreeByThreeChart(),
            "5-2"   => new FiveByTwoChart(),
            "6-1"   => new SixByOneChart(),
            default => throw new InvalidArgumentException("Invalid chart type"),
        };
    }
}