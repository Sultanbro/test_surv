<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    protected $table = 'check_lists';

    public $timestamps = true;

    protected $fillable = [
        'user_name',
        'user_last_name',
        'user_id',
        'auth_id',
        'auth_name',
        'auth_last_name',
        'active_check_text',
        'count_view',
        'role_check',
    ];
}
