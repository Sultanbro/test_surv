<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $table = 'test_results';

    public $timestamps = true;

    protected $casts = [
        'answer' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'test_question_id',
        'answer',
        'status',
        'comment',
        'course_item_model_id' // rename to course_id . Silly mistake
    ];

    CONST PASSED = 1;
    CONST FAILED = 0;
}
