<?php

namespace App\Service\Payment\Core;

use App\Service\Payment\Core\Base\BasePaymentGateway;
use Closure;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Register
{
    private array $gateways = [];

    public function register(
        array|string               $aliases,
        BasePaymentGateway|Closure $instance): Register
    {
        if ($instance instanceof Closure) {
            $instance = $this->resolve($instance);
        }

        foreach (Arr::wrap($aliases) as $key) {
            $this->setGateway($key, $instance);
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function provider(string $alias): BasePaymentGateway
    {
        if ($this->exists($alias)) {
            return $this->getGatewayUseAlias($alias);
        } else {
            throw new InvalidArgumentException("Не известный провайдер $alias");
        }
    }

    public function list(): array
    {
        $list = [];

        foreach ($this->gateways as $alias => $gateway) {
            $path = is_string($gateway) ? $gateway : get_class($gateway);
            $name = Str::remove("Gateway", Str::afterLast($path, "\\"));
            $list[$name]["path"] = $path;
            $list[$name]["aliases"][] = $alias;
        }

        return $list;
    }

    public function exists(string $alias): bool
    {
        return $this->has($alias);
    }

    private function has(string $alias): bool
    {
        return array_key_exists($alias, $this->gateways);
    }

    private function getGatewayUseAlias(string $alias): BasePaymentGateway
    {
        return $this->gateways[$alias];
    }

    private function setGateway(string $key, BasePaymentGateway $instance): void
    {
        $this->gateways[$key] = $instance;
    }

    private function resolve(Closure $instance): BasePaymentGateway
    {
        return $instance(app());
    }

    public function config(array $config): array
    {
        if ($config['gateway'] === 'prodamus') {
            $this->register($config['gateway']);
        }
    }
}