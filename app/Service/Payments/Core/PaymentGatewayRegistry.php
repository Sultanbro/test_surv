<?php

namespace App\Service\Payments\Core;

use Exception;
use Illuminate\Support\Arr;

class PaymentGatewayRegistry
{
    protected array $gateways = [];

    public function register(array|string $name, BasePaymentGateway|callable $instance): PaymentGatewayRegistry
    {
        $instance = is_callable($instance) ? $instance(app()) : $instance;
        foreach (Arr::wrap($name) as $key) {
            $this->gateways[$key] = $instance;
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    function get($name): BasePaymentGateway
    {
        if (in_array($name, $this->gateways)) {
            return $this->gateways[$name];
        } else {
            throw new Exception("Invalid gateway");
        }
    }
}