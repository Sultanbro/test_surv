<?php
declare(strict_types=1);

namespace App\Service\Trigger;

use App\DTO\BaseDTO;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
* Класс для работы с Service.
*/
class AbsentInternshipService
{
    /**
     * @param BaseDTO $dto
     * @return JsonResponse|bool
     * @throws Throwable
     */
    public function handle(BaseDTO $dto): JsonResponse|bool
    {
        $user = User::getUserById($dto->userId);

        $notificationTemplate = MailingNotification::with('recipients')
            ->getTemplates()
            ->where('frequency', MailingEnum::TRIGGER_ABSENT_INTERNSHIP)
            ->first();

        if (!$notificationTemplate)
        {
            return response()->json([
                'message' => 'Template with this type does not exist',
                'status_code' => Response::HTTP_BAD_REQUEST
            ]);
        }

        $link = route('users.edit') . "?id=$dto->userId";
        $message = $notificationTemplate->title;
        $message .= '<br> <a href="' . $link . '" target="_blank"> ' . $user->full_name . ' </a> <br>';

        $mailings = json_decode($notificationTemplate->type_of_mailing);

        foreach ($mailings as $mailing)
        {
            NotificationFactory::createNotification($mailing)->send($notificationTemplate, $message);
        }

        return true;
    }
}