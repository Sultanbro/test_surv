<?php
declare(strict_types=1);

namespace App\Service\Trigger;

use App\DTO\BaseDTO;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\User;
use App\UserNotification;
use Symfony\Component\HttpFoundation\Response;

/**
* Класс для работы с Service.
*/
class ApplyEmployeeService
{
    /**
     * @param BaseDTO $dto
     * @return bool
     */
    public function handle(
        BaseDTO $dto
    ): bool
    {
        $user  = User::getUserById($dto->userId);
        $chart = $user->getWorkChart();

        $notificationTemplate = MailingNotification::with('recipients')
            ->getTemplates()
            ->where('frequency', MailingEnum::TRIGGER_APPLIED)->first();

        $link = route('users.edit') . "?id=$dto->userId";

        $message = $notificationTemplate->title;
        $message .= '<br> <a href="' . $link . '" target="_blank"> ' . $user->full_name . ' </a> <br>';
        $message .= 'Рабочий график: ' . $chart->start_time . '-' . $chart->end_time;

        $recipientIds = MailingFacade::getRecipients($notificationTemplate->id)->pluck('id')->toArray();

        foreach ($recipientIds as $recipientId)
        {
            UserNotification::createNotification(
                $notificationTemplate->name,
                $message,
                $recipientId
            );
        }

        return true;
    }
}