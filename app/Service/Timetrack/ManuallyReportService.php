<?php
declare(strict_types=1);

namespace App\Service\Timetrack;

use App\Repositories\TimeTrackHistoryRepository;
use App\Repositories\TimeTrackingRepository;
use App\Setting;
use App\User;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Класс для работы с Service.
 */
final class ManuallyReportService
{
    public function __construct(
        public TimeTrackingRepository     $timeTrackingRepository,
        public TimeTrackHistoryRepository $historyRepository
    )
    {
    }

    /**
     * @param int $userId
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $time
     * @param string|null $comment
     * @return int
     * @throws Exception|Throwable
     */
    public function handle(
        int     $userId,
        int     $year,
        int     $month,
        int     $day,
        string  $time,
        ?string $comment
    ): int
    {
        $enter = $this->setEnterTime($userId, $year, $month, $day, $time);

        try {
            DB::beginTransaction();
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
            DB::commit();
            return Response::HTTP_CREATED;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $userId
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $time
     * @return string
     * @throws Exception
     */
    private function setEnterTime(
        int    $userId,
        int    $year,
        int    $month,
        int    $day,
        string $time
    ): string
    {
        /** @var User $user */
        $user = User::query()->find($userId);
//        $timezone = new DateTimeZone("UTC");

        return Carbon::create($year, $month, $day)
            ->setTimeFromTimeString($time)
            ->subHours((int)$user->timezone)
            ->format('Y-m-d H:i:s');
    }
}