<?php

namespace App\Models\Tariff;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TariffPayment extends Model
{
    protected $table = 'tariff_payment';

    public $timestamps = true;

    protected $casts = [
        'created_at'  => 'date:d.m.Y H:i',
        'updated_at'  => 'date:d.m.Y H:i',
        'expire_date'  => 'date:d.m.Y',
    ];

    protected $fillable = [
        'owner_id',
        'tariff_id',
        'extra_user_limit',
        'expire_date',
        'auto_payment'
    ];

    /**
     * @return HasOne
     */
    public function tariff(): HasOne
    {
        return $this->hasOne(Tariff::class, 'id', 'tariff_id');
    }

    /**
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Return the tariff payment info with tariff for particular owner.
     *
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getTarriffPaymentsByOwnerId(int $ownerId)
    {
        return $this->with('tariff')
            ->where('owner_id', $ownerId)
            ->get();
    }

    /**
     * Return all the tariff payments from DB.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getTarriffPayments()
    {
        return $this->all();
    }
}
