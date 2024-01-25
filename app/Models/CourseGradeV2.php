<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseGradeV2 extends Model
{
    use HasFactory;

    protected $table = 'course_grades_v2';

    protected $fillable = [
        'user_id', 'course_id', 'curator_id', 'course_grade', 'curator_grade', 'course_comment', 'curator_comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
