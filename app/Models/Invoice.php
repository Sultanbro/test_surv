<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $invoice_id
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
        'invoice_id',
        'actor_name',
        'actor_phone',
        'actor_email',
        'currency',
        'amount'
    ];

    public static function createFromPaymentInvoice(\App\Service\Payment\Core\Invoice $invoice): Invoice
    {
        /** @var Invoice */
        return static::query()->create([
            'invoice_id' => $invoice->getPaymentToken()->token,
            'actor_phone' => $invoice->getParams()['WMI_CUSTOMER_PHONE'],
            'actor_email' => $invoice->getParams()['WMI_CUSTOMER_EMAIL'],
            'currency' => $invoice->getParams()['WMI_CUSTOMER_EMAIL'],
            'amount' => $invoice->getParams()['WMI_PAYMENT_AMOUNT']
        ]);
    }
}
