<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCourseItemProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'course_item_id', 'item_id', 'item_type', 'sub_item_id', 'status', 'completed_at'
    ];
}
