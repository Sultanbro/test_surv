<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseItemV2 extends Model
{
    use HasFactory;

    protected $table = 'course_items_v2';

    protected $fillable = [
        'course_id', 'item_id', 'item_type', 'name', 'order', 'duration'
    ];

    public const BOOK_TYPE = 1;
    public const VIDEO_TYPE = 2;
    public const KB_TYPE = 3;
    public const iSPRING_TYPE = 4;

    public const ITEM_TYPES = [self::BOOK_TYPE, self::VIDEO_TYPE, self::KB_TYPE, self::iSPRING_TYPE];
}
