<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestBonus extends Model
{
    use HasFactory;

    protected $table = 'test_bonuses';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'date', // Y-m-d
        'amount', // int
        'comment', // int
    ];

}
