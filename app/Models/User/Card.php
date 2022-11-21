<?php

namespace App\User;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'user_cards';

    protected $fillable = [
        'user_id',
        'bank',
        'country',
        'cardholder',
        'phone',
        'number',
    ];

}
