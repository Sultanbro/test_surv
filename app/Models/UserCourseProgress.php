<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCourseProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'course_id', 'status', 'progress', 'started_at', 'ended_at'
    ];
}
