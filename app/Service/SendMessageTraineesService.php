<?php

namespace App\Service;

use App\Classes\Helpers\Phone;
use App\Console\Commands\ListenQueue;
use App\Jobs\SendNotificationJob;
use App\Models\Bitrix\Lead;
use App\User;
use App\UserNotification;

class SendMessageTraineesService
{
    /**
     * Send message to new trainee by whatsApp using Jobs for not get block account of wazzup
     * @param array $userIds
     * @return void
     */
    public function handle(array $userIds):void
    {
        //message must be send after two days for that used addDays()
        SendNotificationJob::dispatch($userIds)->delay(now()->addDays(2));
    }

    /**
     * Send message by Jobtron.org to selected users about created new trainees
     * @param array $userIds
     * @param Lead $lead
     * @return void
     */
    public function sendAboutTrainee(array $userIds,Lead $lead,$invite_at): void
    {
        $title = "Новый стажер!";
        $message = "К Вам поступил новый стажер на ".$invite_at." не пропустите его ".$lead->name." и обязательно добавьте в группу ватцап по номеру ";
        $message .="https://api.whatsapp.com/send/?phone=".Phone::normalize($lead->phone)."&text&app_absent=0";
        foreach ($userIds as $userId)
        {
            // Create notification for selected users about new trainees
            UserNotification::createNotification($title,$message,$userId);
        }

    }
}