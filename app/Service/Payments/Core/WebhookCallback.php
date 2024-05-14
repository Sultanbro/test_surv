<?php

namespace App\Service\Payments\Core;

abstract class WebhookCallback
{
    abstract public function handle(): WebhookCallbackResponse;
}