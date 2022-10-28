<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Класс отвечает за таблицу taxes.
 * Таблица на налог каждого сотрудника.
 */
class Tax extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'taxes';

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'percent'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Получаем сотрудника на кого привязан налог начисления.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
