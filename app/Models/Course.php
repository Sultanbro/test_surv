<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseItem;

class Course extends Model
{
    protected $table = 'courses';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'img',
        'text',
    ];

    public function items()
    {
        return $this->hasMany(CourseItem::class, 'course_id')->orderBy('order');
    }
}
