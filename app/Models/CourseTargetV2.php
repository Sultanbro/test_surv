<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTargetV2 extends Model
{
    use HasFactory;

    protected $table = 'course_targets_v2';

    protected $fillable = [
        'course_id', 'target_type', 'target_id'
    ];
}

