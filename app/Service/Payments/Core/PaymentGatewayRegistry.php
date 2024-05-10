<?php

namespace App\Service\Payments\Core;

use Closure;
use Exception;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PaymentGatewayRegistry
{
    protected array $gateways = [];

    public function register(array|string $aliases, BasePaymentGateway|Closure $instance): PaymentGatewayRegistry
    {
        if ($instance instanceof Closure) {
            $instance = $instance(app());
        }

        foreach (Arr::wrap($aliases) as $key) {
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

    function list(): array
    {
        $list = [];

        foreach ($this->gateways as $name => $gateway) {
            $className = is_string($gateway) ? $gateway : get_class($gateway);
            $list[$className]["aliases"][] = $name;
        }

        return $list;
    }
}