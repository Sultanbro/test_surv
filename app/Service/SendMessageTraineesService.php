<?php

namespace App\Service;

use App\Classes\Helpers\Phone;
use App\Jobs\SendNotificationJob;
use App\Models\Bitrix\Lead;
use App\UserNotification;

class SendMessageTraineesService
{
    /**
     * Send message to new trainee by whatsApp using Jobs for not get block account of wazzup
     * @param array $userIds
     * @return void
     */
    public function handle(array $userIds): void
    {
        //message must be send after two days for that used addDays()
        SendNotificationJob::dispatch($userIds)->delay(now()->addDays(2));
    }

    /**
     * Send message by Jobtron.org to selected users about created new trainees
     * @param array $userIds
     * @param Lead $lead
     * @param $invite_at
     * @return void
     */
    public function sendAboutTrainee(array $userIds, Lead $lead, $invite_at): void
    {
        $title = "Новый стажер!";
        $link = "https://api.whatsapp.com/send/?phone=" . Phone::normalize($lead->phone) . "&text&app_absent=0";
        $message = "Новый стажер " . $lead->name . " не пропустите его \n На " . $invite_at->format('d.m.Y') . " время начало обучения " . $invite_at->format("H:i") . " \n ссылка на ватцап ";
        $message .= "<a href='$link'>написать стажеру в вацап <i class='fa fab fa-whatsapp'></i></a>";
        foreach ($userIds as $userId) {
            // Create notification for selected users about new trainees
            UserNotification::createNotification($title, $message, $userId);
        }
    }
}
