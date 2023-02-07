<?php

namespace App\Models;


use App\Enums\ErrorCode;
use App\Support\Core\CustomException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use HasFactory;

    public function scopeGetByNameOrFail($query, string $name)
    {
        $role = $query->where('name', $name)->first();

        if ($role == null)
        {
            new CustomException("Role by name $name is not exist", ErrorCode::BAD_REQUEST, []);
        }

        return $role;
    }
}
