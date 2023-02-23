<?php

namespace App\Models\Tariff;

use App\Traits\CurrencyTrait;
use App\User;
use YooKassa\Model\Receipt;
use YooKassa\Model\ReceiptItem;

final class TariffPrice {

    use CurrencyTrait;

    private static $priceForOnePersonInKzt = (float) config('payment')['payment_for_one_person'];

    private static $currencyMap = [
        'kzt' => [
            'rub' => 'kztToRub',
        ],
    ];

    public int $tariffPrice;
    public int $extraUsersPrice;
    public int $priceForOnePerson;

    private $currency = 'kzt';

    /**
     * @param Tariff $tariff
     */
    public function __construct(
        private Tariff $tariff,
        public int $extraUsers
    )
    {
        $this->setKztPrices();
    }

    public function getTotal(): float {
        return $this->tariffPrice + $this->extraUsersPrice;
    }

    public function setCurrency(string $newCurrency): self {
        $oldCurrency = $this->currency;

        if ($newCurrency == $oldCurrency) {
            return $this;
        }

        $this->currency = $newCurrency;

        if ($newCurrency == 'kzt') {
            $this->setKztPrices();
            return $this;
        }

        $convertMethod = (self::$currencyMap[$oldCurrency])[$newCurrency];

        $this->tariffPrice = $this->{$convertMethod}($this->tariffPrice);
        $this->priceForOnePerson = $this->{$convertMethod}($this->priceForOnePerson);
        $this->updateExtraUsersPrice();

        return $this;
    }

    private function setKztPrices(): void
    {
        $this->tariffPrice = $this->tariff->price;
        $this->priceForOnePerson = self::$priceForOnePersonInKzt;
        $this->updateExtraUsersPrice();
    }

    private function updateExtraUsersPrice(): void
    {
        $this->extraUsersPrice = $this->extraUsers * $this->priceForOnePerson;
    }

    public function kztToRub(float $v): float
    {
        return $this::converterToRub($v);
    }

    public function createYooKassaReceipt(User $user): Receipt
    {
        if ($this->currency != 'rub') {
            throw new \Exception('cannot createYooKassaReceipt: wrong currency');
        }

        $tariffKind = $this->tariff->kind;

        $receipt = new Receipt(array(
            'customer' => array(
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone
            ),
            'items' => array(
                array(
                    'description'   =>  "Покупка тарифа $tariffKind",
                    'quantity'      => 1,
                    'amount' => array(
                        'value'     => $this->tariffPrice,
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
            ),
        ));

        $extraUsers = $this->extraUsers;

        if ($extraUsers > 0) {
            $extraUsersReceiptItem = new ReceiptItem(array(
                'description'   =>  "Кол-во пользователей: $extraUsers, цена за одного пользователя $this->priceForOnePerson.",
                'quantity'      => $extraUsers,
                'amount' => array(
                    'value'     => $this->extraUsersPrice,
                    'currency'  => 'RUB'
                ),
                'vat_code' => '1',
                'payment_mode' => 'full_payment',
                'payment_subject' => 'service',
                'supplier' => array(
                    'name' => 'string',
                    'phone' => 'string'
                )
            ));

            $receipt->addItem($extraUsersReceiptItem);
        }

        return $receipt;
    }
}