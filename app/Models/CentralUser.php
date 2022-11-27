<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

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

    /**
     * Кабинеты user-a
     */
    public function tenants(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user', 'tenant_id', 'user_id');
    }
}
