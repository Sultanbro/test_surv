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
        'question_id',
        'answer',
        'status',
        'course_item_model_id'
    ];

    CONST PASSED = 1;
    CONST FAILED = 0;
}
