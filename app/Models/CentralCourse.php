<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentralCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'central_courses';

    protected $casts = [
        'slides',
    ];

    protected $fillable = [
        'tenant_id', 'cat_id', 'price', 'for_sale', 'author', 'slides', 'verified_at', 'verified_by'
    ];

    public function tenantCourse()
    {
        return $this->hasOne(CourseV2::class);
    }

    public function tenantCourseItems()
    {
        return $this->hasManyThrough(CourseItemV2::class, CourseV2::class);
    }

    public function tenantCourseGrades()
    {
        return $this->hasManyThrough(CourseGradeV2::class, CourseV2::class);
    }
}
