<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'user_id',
        'manager_id',
        'date'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    /**
     * Получить ответственных.
     * @return BelongsTo
     */
    public function managers(): BelongsTo
    {
        return $this->belongsTo('App\User', 'manager_id', 'id');
    }

    /**
     * Получить сотрудников.
     * @return BelongsTo
     */
    public function trainees(): BelongsTo
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
