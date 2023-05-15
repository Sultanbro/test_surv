<?php
declare(strict_types=1);

namespace App\Service\Trigger;

use App\DTO\BaseDTO;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\User;
use App\UserNotification;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
* Класс для работы с Service.
*/
class ApplyEmployeeService
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
        $user  = User::getUserById($dto->userId);
        $chart = $user->getWorkChart();

        $notificationTemplate = MailingNotification::with('recipients')
            ->getTemplates()
            ->where('frequency', MailingEnum::TRIGGER_APPLIED)->where('days', '[]')->first();

        $types = json_decode($notificationTemplate->type_of_mailing);

        $link = route('users.edit') . "?id=$dto->userId";

        $message = $notificationTemplate->title;
        $message .= '<br> <a href="' . $link . '" target="_blank"> ' . $user->full_name . ' </a> <br>';
        $message .= 'Рабочий график: ' . $chart->start_time . '-' . $chart->end_time;

        foreach ($types as $type)
        {
            NotificationFactory::createNotification($type)->send($notificationTemplate, $message);
        }

        return true;
    }
}