<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    protected $connection = 'tenant';
}
