<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    protected $table = 'user_courses';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'user_id',
    ];
}
