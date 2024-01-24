<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralCourse extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'central_courses';

    protected $casts = [
        'slides',
    ];

    protected $fillable = [
        'tenant_id', 'cat_id', 'price', 'for_sale', 'author', 'slides', 'verified_at', 'verified_by'
    ];
}
