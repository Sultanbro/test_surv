<?php

namespace App\Models;

use App\Classes\Helpers\Phone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $transaction_id
 * @property string $payer_name
 * @property string $payer_phone
 * @property string $amount
 * @property string $status
 * @property string $name
 * @property string $url
 * @property string $provider
 */
class Invoice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'invoices';
    protected $fillable = [
        'transaction_id',
        'payer_name',
        'payer_phone',
        'amount',
        'status',
        'name',
        'url',
        'provider'

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
        static::query()->update([
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
