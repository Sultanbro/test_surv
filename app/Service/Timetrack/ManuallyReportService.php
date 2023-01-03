<?php
declare(strict_types=1);

namespace App\Service\Timetrack;

use App\Repositories\TimeTrackHistoryRepository;
use App\Repositories\TimeTrackingRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
* Класс для работы с Service.
*/
final class ManuallyReportService
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
     * @return int
     * @throws Exception
     */
    public function handle(
        int $userId,
        int $year,
        int $month,
        int $day,
        string $time,
        ?string $comment
    ): int
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

            return Response::HTTP_CREATED;

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
        return Carbon::create($year, $month, $day)->setTimeFromTimeString($time)->subHours(6)->format('Y-m-d H:i:s');
    }
}