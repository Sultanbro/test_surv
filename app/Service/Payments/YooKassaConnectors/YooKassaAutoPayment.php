<?php

namespace App\Service\Payments\YooKassaConnectors;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\AutoPayment;
use App\User;
use Exception;
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
use YooKassa\Model\CurrencyCode;
use YooKassa\Request\Payments\CreatePaymentRequest;

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
     * @throws Exception
     */
    public function makeAutoPayment(TariffPayment $tariffPayment): void
    {
        $tariff = Tariff::getTariffById($tariffPayment->tariff_id);
        $user   = User::getAuthUser($tariffPayment->owner_id);
        $price  = $tariff
            ->getPrice($tariffPayment->extra_user_limit)
            ->setCurrency('rub');

        $builder = CreatePaymentRequest::builder();

        $builder->setAmount(array(
            'value' => $price->getTotal(),
            'currency' => CurrencyCode::RUB,
        ));
        $builder->setCapture(true);
        $builder->setPaymentMethodId($tariffPayment->payment_id);
        $builder->setDescription('Заказ №' . time());
        $builder->setReceipt($price->createYoKassReceipt($user));

        $idempotenceKey = uniqid('', true);

        $this->client->createPayment(
            $builder->build(),
            $idempotenceKey,
        );
    }
}