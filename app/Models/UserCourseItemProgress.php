<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseItemProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'item_id', 'item_type', 'sub_item_id', 'status', 'completed_at'
    ];
}
