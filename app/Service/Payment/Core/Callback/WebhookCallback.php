<?php

namespace App\Service\Payment\Core\Callback;

abstract class WebhookCallback
{
    abstract public function handle(): WebhookCallbackResponse;
}