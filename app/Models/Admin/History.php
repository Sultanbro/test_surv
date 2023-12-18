<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'admin_history';

    public $timestamps = false;

    protected $fillable = [
        'author',
        'author_id',
        'action',
        'data',
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    const AUTHOR_NONE = 0;
    const SYSTEM = 1;
    const USER = 2;
    const SERVICE = 3;

    const AUTHORS = [
        'UNKNOWN' => 0,
        'BITRIX' => 1,
        'INTELLECT' => 2,
    ];

    public static function system(string $action, $data = [])
    {
        self::create([
            'author' => self::SYSTEM,
            'author_id' => 0,
            'action' => $action,
            'data' => $data,
        ]);
    }

    public static function user($user_id, string $action, $data = [])
    {
        self::create([
            'author' => self::USER,
            'author_id' => $user_id,
            'action' => $action,
            'data' => $data,
        ]);
    }

    public static function bitrix(string $action, $data = [])
    {
        self::create([
            'author' => self::SERVICE,
            'author_id' => self::AUTHORS['BITRIX'],
            'action' => $action,
            'data' => $data,
        ]);
    }

    public static function intellect(string $action, $data = [])
    {
        self::create([
            'author' => self::SERVICE,
            'author_id' => self::AUTHORS['INTELLECT'],
            'action' => $action,
            'data' => $data,
        ]);
    }

    public static function lead(array $bitrixLead): void
    {
        self::query()->create([
            'author' => self::SERVICE,
            'author_id' => self::AUTHORS['INTELLECT'],
            'action' => "Получение лида из битрикс",
            'data' => $bitrixLead,
        ]);
    }
}
