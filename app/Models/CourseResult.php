<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseResult extends Model
{
    protected $table = 'course_results';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress', // 0 - 100
    ];

    // status
    CONST INITIAL = 0;
    CONST COMPLETED = 1;
    CONST STARTED = 2;
}
