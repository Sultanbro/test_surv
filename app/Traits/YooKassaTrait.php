<?php

namespace App\Traits;

use App\DTO\Api\DoPaymentDTO;
use App\DTO\Api\StatusPaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Events\PaymentIsSuccessEvent;
use App\Models\Tariff\Tariff;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use naffiq\tenge\CurrencyRates;
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

trait YooKassaTrait
{
    /**
     * @var Client
     */
    public Client $client;

    /**
     * @param int $merchantId
     * @param string $secretKey
     * @return void
     */
    public function __construct(
        public int $merchantId,
        public string $secretKey
    )
    {
        $this->client = new Client();
        $this->client->setAuth($this->merchantId, $this->secretKey);
    }

    /**
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param bool $autoPayment
     * @return string
     * @throws Exception
     */
    public function pay(
        int $tariffId,
        int $extraUsersLimit,
        bool $autoPayment = false
    ): string
    {
        try {
            $idempotenceKey = uniqid('', true);
            $response = $this->client->createPayment(
                $this->getPaymentRequest($tariffId, $extraUsersLimit, $autoPayment),
                $idempotenceKey
            );

            //получаем confirmationUrl для дальнейшего редиректа
            return $response->getConfirmation()->getConfirmationUrl();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param StatusPaymentDTO $dto
     * @return bool
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function paymentInfoSave(StatusPaymentDTO $dto): bool
    {
        try {
            $paymentStatus = $this->client->getPaymentInfo($dto->paymentId);

            if ($paymentStatus->status != PaymentStatusEnum::STATUS_SUCCESS)
            {
                new CustomException("Оплата по платежу $dto->paymentId еще не сделана", ErrorCode::BAD_REQUEST, []);
            }

            PaymentIsSuccessEvent::dispatch(
                $dto->tariffId,
                $dto->extraUsersLimit,
                $dto->paymentId,
                $dto->paymentType,
                $dto->autoPayment
            );

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param bool $autoPayment
     * @return array
     * @throws Exception
     */
    private function getPaymentRequest(
        int $tariffId,
        int $extraUsersLimit,
        bool $autoPayment = false
    ): array
    {
        $tariff = Tariff::getTariffById($tariffId);
        $this->converterToRub((int)$tariff->price);
        return array(
            'amount' => array(
                'value'     => $this->converterToRub((int)$tariff->price),
                'currency'  => 'RUB',
            ),
            'confirmation' => array(
                'type'          => 'redirect',
                'locale'        => 'ru_RU',
                'return_url'    => 'https://jobtron.org/',
            ),
            'capture'           => true,
            'description'       => 'Заказ №' . time(),
            'save_payment_method' => $autoPayment,
            'metadata' => array(
                'orderNumber'   => time()
            ),
            'receipt' => array(
                'customer' => array(
                    'full_name' => $this->getUser()->full_name,
                    'email'     => $this->getUser()->email,
                    'phone'     => $this->getUser()->phone
                ),
                'items' => array(
                    array(
                        'description'   =>  "Покупка тарифа $tariff->kind",
                        'quantity'      => $extraUsersLimit,
                        'amount' => array(
                            'value'     => $this->converterToRub((int)$tariff->price),
                            'currency'  => 'RUB'
                        ),
                        'vat_code' => '1',
                        'payment_mode' => 'full_payment',
                        'payment_subject' => 'service',
                        'supplier' => array(
                            'name' => 'string',
                            'phone' => 'string'
                        )
                    ),
                )
            )
        );
    }

    /**
     * @throws Exception
     */
    private function converterToRub(
        int $price
    ): float
    {
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL);
        return $rates->convertFromTenge('RUB', $price);
    }

    /**
     * @return ?object
     */
    private function getUser(): ?object
    {
        return User::query()->find(5);
    }
}