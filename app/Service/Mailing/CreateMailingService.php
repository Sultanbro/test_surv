<?php
declare(strict_types=1);

namespace App\Service\Mailing;

use App\DTO\BaseDTO;
use App\DTO\Mailing\CreateMailingDTO;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class CreateMailingService
{
    /**
     * @param BaseDTO $dto
     * @return bool
     * @throws Throwable
     */
    public function handle(
        BaseDTO $dto
    ): bool
    {
        $days = $dto->date['frequency'] === MailingEnum::MONTHLY ? MailingFacade::daysOfMonth($dto->date['days']) : $dto->date['days'];

        DB::transaction(function () use ($dto, $days){
            $notification =MailingFacade::createNotification(
                $dto->name,
                $dto->title,
                $dto->typeOfMailing,
                $days,
                $dto->date['frequency'],
                $dto->isTemplate,
                $dto->count
            );

            $recipients = MailingFacade::recipients($dto->recipients, $notification->id);

            MailingFacade::insertSchedule($recipients);
        });

        return true;
    }
}