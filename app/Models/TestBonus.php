<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestBonus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'test_bonuses';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'date', // Y-m-d
        'amount', // int
        'comment', // int
        'course_item_id', // int
    ];

}
