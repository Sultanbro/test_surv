<?php

namespace App\Service\Payments\Core;

use Exception;
use Illuminate\Support\Arr;
use InvalidArgumentException;

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
        if (array_key_exists($name, $this->gateways)) {
            return $this->gateways[$name];
        } else {
            throw new InvalidArgumentException("Не известный провайдер $name");
        }
    }
}