<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use  Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    /**
     * @return BelongsToMany
     */
    public function usersAccess(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user', 'permission_id', 'user_id')
            ->withPivot('is_access')->withTimestamps();
    }
}