<?php

namespace App\Models;


use App\Enums\ErrorCode;
use App\Support\Core\CustomException;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use HasFactory;

    /**
     * @throws Exception
     */
    public function scopeGetByNameOrFail($query, string $name)
    {
        $role = $query->where('name', $name)->first();

        if ($role == null)
        {
            throw new Exception("Role by name $name is not exist");
        }

        return $role;
    }
}
