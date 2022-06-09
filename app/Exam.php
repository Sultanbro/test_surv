<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
//    protected $connection = 'polygon';
    protected $table = 'exams';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'book_id',
        'exam_date',
        'success',
        'link',
        'month',
        'year',
        'bonus'
    ];

}
