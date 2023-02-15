<?php

namespace App\Service\Payments\YooKassaConnectors;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\AutoPayment;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;

class YooKassaAutoPayment implements AutoPayment
{
    /**
     * @param Client $client
     */
    public function __construct(
        public Client $client
    )
    {}

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws ExtensionNotFoundException
     * @throws BadApiRequestException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function makeAutoPayment(TariffPayment $tariffPayment): void
    {
        $tariff = Tariff::getTariffById($tariffPayment->tariff_id);

        $this->client->createPayment(
            array(
                'amount' => array(
                    'value' => $tariff->calculateTotalPrice($tariffPayment->extra_user_limit),
                    'currency' => 'RUB',
                ),
                'capture' => true,
                'payment_method_id' => $tariffPayment->payment_id,
                'description' => 'Заказ №' . time(),
            ),
            uniqid('', true)
        );
    }
}