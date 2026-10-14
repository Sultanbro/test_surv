<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModel extends Model
{
    use HasFactory;

    protected $table = 'course_model';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'item_id',
        'item_model',
    ];


}
