<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentralCourseCat extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'central_courses';

    protected $fillable = [
        'name', 'order'
    ];

    public function courses()
    {
        return $this->hasMany(CentralCourse::class, 'cat_id');
    }
}
