<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    protected $table = 'course_progresses';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'item_id',
        'item_model', // video kb book
        'status', // 1 0
    ];
}
