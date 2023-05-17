<?php
declare(strict_types=1);

namespace App\Service\Mailing;

use App\DTO\Mailing\UpdateMailingDTO;
use App\Models\Mailing\MailingNotification;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class UpdateMailingService
{
    /**
     * @throws Throwable
     */
    public function handle(
        UpdateMailingDTO $dto
    ): bool
    {
        $notification = MailingNotification::getById($dto->id);

        return $notification->update([
            'name'              => $dto->name,
            'title'             => $dto->title,
            'days'              => $dto->date['days'],
            'frequency'         => $dto->date['frequency'],
            'type_of_mailing'   => $dto->typeOfMailing,
            'is_template'       => $dto->isTemplate,
            'status'            => $dto->status
        ]);
    }
}