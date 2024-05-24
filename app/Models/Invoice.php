<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $transaction_id
 * @property string $actor_name
 * @property string $actor_phone
 * @property string $actor_email
 * @property string $currency
 * @property string $amount
 */
class Invoice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'invoices';
    protected $fillable = [
        'transaction_id',
        'actor_name',
        'actor_phone',
        'actor_email',
        'currency',
        'amount',
        'status',
        'gateway'
    ];

    public static function new(\App\Service\Payment\Core\Webhook\Invoice $invoice, string $gateway): Invoice
    {
        /** @var Invoice */
        return static::query()->create([
            'transaction_id' => $invoice->getTransaction()->id,
            'actor_phone' => $invoice->getParams()['WMI_CUSTOMER_PHONE'],
            'actor_email' => $invoice->getParams()['WMI_CUSTOMER_EMAIL'],
            'currency' => $invoice->getParams()['WMI_CUSTOMER_EMAIL'],
            'amount' => $invoice->getParams()['WMI_PAYMENT_AMOUNT'],
            'gateway' => $gateway,
        ]);
    }

    public function setStatusSuccess(): Invoice
    {
        /** @var Invoice */
        return static::query()->update([
            'status' => 'success'
        ]);
    }

    public function setStatusFailed(): Invoice
    {
        /** @var Invoice */
        return static::query()->update([
            'status' => 'failed'
        ]);
    }
}
