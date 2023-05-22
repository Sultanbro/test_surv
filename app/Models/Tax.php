<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'name',
        'value',
        'is_percent'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Получаем сотрудника на кого привязан налог начисления.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tax')
            ->withPivot(['value'])
            ->withTimestamps();
    }

    /**
     * @param int $id
     * @return Tax
     */
    public static function getTaxById(
        int $id
    ): Tax
    {
        return self::query()->findOrFail($id);
    }
}
