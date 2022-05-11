<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseItem extends Model
{
    protected $table = 'course_items';

    public $timestamps = true;

    protected $fillable = [
        'course_id',
        'item_id',
        'item_model',
        'order',
        'title',
    ];

    // item_model
    CONST MODEL_VIDEO = 'video';
    CONST MODEL_KB = 'kb';
    CONST MODEL_BOOK = 'book';
}
