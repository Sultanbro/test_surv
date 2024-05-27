<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $transaction_id
 * @property string $payer_name
 * @property string $payer_phone
 * @property string $amount
 * @property string $status
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
        'status'
    ];

    public function setStatusSuccess(): void
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
