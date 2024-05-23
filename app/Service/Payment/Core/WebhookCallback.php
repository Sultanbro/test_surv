<?php

namespace App\Service\Payment\Core;

abstract class WebhookCallback
{
    abstract public function handle(): WebhookCallbackResponse;
}