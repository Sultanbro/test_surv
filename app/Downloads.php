<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Downloads extends Model
{
    public $timestamps = true;

    protected $table = 'profile_downloads';

    protected $fillable = [
        'user_id',
        'ud_lich',
        'dog_okaz_usl',
        'sohr_kom_tainy',
        'dog_o_nekonk',
        'trud_dog',
        'archive',
        'resignation',
    ];

}
