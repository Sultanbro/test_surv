<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    public $timestamps = true;

    protected $table = 'profile_photo';

    protected $fillable = [
        'user_id',
        'path'
    ];
}
