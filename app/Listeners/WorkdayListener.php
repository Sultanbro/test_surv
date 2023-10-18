<?php

namespace App\Listeners;

use App\Events\WorkdayEvent;
use App\Models\WorkChart\Workday;
use App\Repositories\WorkChart\WorkdayRepository;
use Exception;
use Illuminate\Support\Carbon;

class WorkdayListener
{
    /**
     * @var WorkdayRepository
     */
    private WorkdayRepository $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new WorkdayRepository;
    }

    /**
     * Handle the event.
     *
     * @param WorkdayEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(WorkdayEvent $event): void
    {
        $group = $event->user->activeGroup();
        if ($group) {
            $chart = $group->workChart()->first();
        }
        if ($event->user->workChart()->exists()) {
            $chart = $event->user->workChart()->first();
        }

        $date = Carbon::now();

        $weekRecords = $this->repository->getUserWorkDaysPerWeek($event->user->id, $date->format('Y-m-d'), $date->weekNumberInMonth);

//        ChartFactory::make($chart->name)->chartProcess($weekRecords);     Проверить логику

        $exist = $this->repository->getUserWorkDaysPerWeek($event->user->id, $date->format('Y-m-d'), $date->weekNumberInMonth, $date->dayOfWeek)->exists();

        if (!$exist) {
            Workday::createOrFail(
                $event->user->id,
                $date->dayOfWeek,
                $date->format('Y-m-d'),
                $date->weekNumberInMonth
            );
        }
    }
}
