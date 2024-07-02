<?php

namespace App\Listeners;

use App\Events\UserStatUpdatedEvent;
use App\Repositories\KpiBonusRepository;
use App\Service\Bonus\BonusManager;
use Carbon\Carbon;
use Exception;

class UserStatUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        public KpiBonusRepository $repository
    )
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserStatUpdatedEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(UserStatUpdatedEvent $event): void
    {
        $dto = $event->dto;
        $date = Carbon::createFromDate($dto->year, $dto->month, $dto->day)->format('Y-m-d');
        $bonuses = $this->repository->getBonusByUserModelPerDate($dto->employeeId, $date)->get();

        foreach ($bonuses as $bonus)
        {
            BonusManager::call($bonus->unit)->calculate($bonus, $dto);
        }
    }
}
