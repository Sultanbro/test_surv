<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_user';

    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'user_id',
    ];

    public function user()
    {
        return $this->hasMany('App\User', 'user_id');
    }
}
