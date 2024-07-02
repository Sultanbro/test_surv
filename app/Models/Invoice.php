<?php

namespace App\Models;

use App\Classes\Helpers\Phone;
use App\Enums\Invoice\InvoiceType;
use App\Enums\Payments\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $transaction_id
 * @property string $lead_id
 * @property string $payer_name
 * @property string $payer_phone
 * @property string $amount
 * @property string $status
 * @property string $name
 * @property string $url
 * @property string $provider
 * @property InvoiceType $type
 * @property array $payload
 */
class Invoice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'invoices';
    protected $fillable = [
        'transaction_id',
        'lead_id',
        'payer_name',
        'payer_phone',
        'amount',
        'status',
        'name',
        'url',
        'provider',
        'type',
        'payload',
    ];

    protected $casts = [
        'type' => InvoiceType::class,
        'payload' => 'array',
    ];

    public static function getByTransactionId(int|string $getTransactionId): ?Invoice
    {
        /** @var Invoice */
        return self::query()
            ->whereNot('status', 'success')
            ->where('transaction_id', $getTransactionId)
            ->first();
    }

    public static function getByPayerPhone(string $phone): ?Invoice
    {

        /** @var Invoice */
        return self::query()
            ->whereNot('status', 'success')
            ->where('payer_phone', Phone::normalize($phone))
            ->first();
    }

    public function updateStatusToSuccess(): void
    {
        $this->update([
            'status' => PaymentStatusEnum::STATUS_SUCCESS
        ]);
    }

    public function setStatusFailed(): Invoice
    {
        /** @var Invoice */
        return $this->update([
            'status' => 'failed'
        ]);
    }
}
