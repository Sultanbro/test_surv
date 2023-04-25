<?php

namespace App\Service\Mailing\Types;

class NotifyManager
{
    private Notify $notify;

    /**
     * @param Notify $notify
     * @return void
     */
    public function setNotifier(
        Notify $notify
    ): void
    {
        $this->notify = $notify;
    }

    /**
     * @return Notify
     */
    public function executeNotification(): Notify
    {
        return $this->notify->doNotify();
    }
}