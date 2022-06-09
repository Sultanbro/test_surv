<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
