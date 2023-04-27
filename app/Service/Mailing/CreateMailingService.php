<?php
declare(strict_types=1);

namespace App\Service\Mailing;

use App\DTO\BaseDTO;
use App\DTO\Mailing\CreateMailingDTO;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class CreateMailingService
{
    /**
     * @param BaseDTO<CreateMailingDTO> $dto
     * @return bool
     * @throws Throwable
     */
    public function handle(
        BaseDTO $dto
    ): bool
    {
        DB::transaction(function () use ($dto){
            $notification =MailingFacade::createNotification(
                $dto->name,
                $dto->title,
                $dto->typeOfMailing,
                $dto->date['frequency'],
                $dto->time
            );

            foreach ($dto->recipients as $recipient)
            {
                if ($recipient['type'] == 1)
                {
                    MailingFacade::createSchedule($recipient['id'], MailingEnum::USER, $notification->id, $dto->date['days']);
                }

                if ($recipient['type'] == 2)
                {
                    MailingFacade::createSchedule($recipient['id'], MailingEnum::GROUP, $notification->id, $dto->date['days']);
                }

                if ($recipient['type'] == 3)
                {
                    MailingFacade::createSchedule($recipient['id'], MailingEnum::POSITION, $notification->id, $dto->date['days']);
                }
            }
        });

        return true;
    }
}