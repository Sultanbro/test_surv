<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int user_id
 * @property string bank
 * @property string country
 * @property string cardholder
 * @property string phone
 * @property string iban
 * @property string number
 */
class Card extends Model
{
    protected $table = 'user_cards';

    protected $fillable = [
        'user_id',
        'bank',
        'country',
        'cardholder',
        'phone',
        'iban',
        'number',
    ];

}
