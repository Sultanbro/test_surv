<?php

namespace App\Service\Timetrack;

use App\Repositories\TimeTrackHistoryRepository;
use App\Repositories\TimeTrackingRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

/**
* Класс для работы с Service.
*/
class ManuallyReportService
{
    public function __construct(
        public TimeTrackingRepository $timeTrackingRepository,
        public TimeTrackHistoryRepository $historyRepository
    )
    {}

    /**
     * @param int $userId
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $time
     * @param string|null $comment
     * @return void
     * @throws Exception
     */
    public function handle(
        int $userId,
        int $year,
        int $month,
        int $day,
        string $time,
        ?string $comment
    )
    {
        $enter = $this->setEnterTime($year, $month, $day, $time);
        try {

            DB::transaction(function () use ($userId, $year, $month, $day, $time, $enter, $comment) {
                $updateOrCreate = $this->timeTrackingRepository->updateOrCreate(
                    $userId,
                    $year,
                    $month,
                    $day,
                    $time,
                    $enter,
                    $comment
                );

                $this->historyRepository->createHistory($userId, $updateOrCreate, $enter);
            });

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $time
     * @return string
     */
    private function setEnterTime(
        int $year,
        int $month,
        int $day,
        string $time
    ): string
    {
        return Carbon::create($year, $month, $day)->setTimeFromTimeString($time)->format('Y-m-d H:i:s');
    }
}