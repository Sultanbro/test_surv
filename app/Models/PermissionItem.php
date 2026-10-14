<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionItem extends Model
{
    use HasFactory; 

    public $timestamps = true;

    protected $table = 'permission_items';

    protected $casts = [
        'targets' => 'array',
        'roles' => 'array',
        'groups' => 'array',
    ];

    protected $fillable = [
        'targets',
        'roles',
        'groups',
        'groups_all', // Все группы
    ];

}
