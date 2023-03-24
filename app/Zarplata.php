<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Zarplata extends Model
{
    protected $table = 'zarplata';
    
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'zarplata',
        'card_number',
        'jysan',
        'card_jysan',
        'jysan_cardholder',
        'kaspi',
        'card_kaspi',
        'kaspi_cardholder',
    ];


    public function partner()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @param int $userId
     * @return Model
     */
    public static function getSalaryByUserId(
        int $userId
    ): Model
    {
        return self::query()->where('user_id', $userId)->first();
    }

    /**
     * @param array<int> $userIds
     * @return Collection<Zarplata>
     */
    public static function getSalaryByUserIds(
        array $userIds
    ): Collection
    {
        return self::query()->whereIn('user_id', $userIds)->get();
    }
}
