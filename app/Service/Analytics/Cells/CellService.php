<?php
declare(strict_types=1);

namespace App\Service\Analytics\Cells;

use App\Repositories\Analytics\AnalyticColumnRepository;
use App\Repositories\Analytics\AnalyticStatRepository;
use Carbon\Carbon;

class CellService
{
    public function __construct(
        public AnalyticColumnRepository $columnRepository,
        public AnalyticStatRepository $statRepository
    )
    {}

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return string
     */
    public function getDate(
        int $year,
        int $month,
        int $day = 1
    ): string
    {
        return Carbon::createFromDate($year,$month, $day)->format('Y-m-d');
    }
}