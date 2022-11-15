<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_user';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'user_id',
        'from',
        'to',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    const STATUS_DROP = 'drop';
    const STATUS_ACTIVE = 'active';
    const STATUS_FIRED = 'fired';

    public function user()
    {
        return $this->hasMany('App\User', 'user_id');
    }
}
