<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralUser extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'deleted_at',
        'password',
    ];
}
