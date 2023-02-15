<?php

namespace App\Service\Payments\YooKassaConnectors;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\AutoPayment;
use App\Traits\CurrencyTrait;
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

class YooKassaAutoPayment implements AutoPayment
{
    use CurrencyTrait;

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
        $user   = User::getAuthUser();
        $price  = $tariff->calculateTotalPrice($tariff->id, $tariffPayment->extra_user_limit);
        $priceToRub = $this->converterToRub($price);

        $priceForOnePerson = env('PAYMENT_FOR_ONE_PERSON');

        $this->client->createPayment(
            array(
                'amount' => array(
                    'value' => $priceToRub,
                    'currency' => 'RUB',
                ),
                'capture' => true,
                'payment_method_id' => $tariffPayment->payment_id,
                'description' => 'Заказ №' . time(),
                'receipt' => array(
                    'customer' => array(
                        'full_name' => $user->full_name,
                        'email'     => $user->email,
                        'phone'     => $user->phone
                    ),
                    'items' => array(
                        array(
                            'description'   =>  "Покупка тарифа $tariff->kind",
                            'quantity'      => 1,
                            'amount' => array(
                                'value'     => $priceToRub,
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
                        array(
                            'description'   =>  "Кол-во пользователей: $tariffPayment->extra_user_limit, цена за одного пользователя $priceForOnePerson." ,
                            'quantity'      => $tariffPayment->extra_user_limit,
                            'amount' => array(
                                'value'     => $priceToRub,
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
            ),
            uniqid('', true)
        );
    }
}