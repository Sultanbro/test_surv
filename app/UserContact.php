<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    protected $fillable = [
        'type',
        'name',
        'value',
        'user_id'
    ];
}
