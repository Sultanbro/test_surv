<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CentralUser extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'users';
    
    protected $appends = ['full_name'];

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'deleted_at',
        'password',
        'birthday',
        'login_at',
        'lead',
        'country',
        'city',
        'balance',
    ];

    /**
     * Кабинеты user-a где он owner
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user', 'user_id', 'tenant_id');
    }

    /**
     * Кабинеты user-a 
     */
    public function cabinets(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_pivot', 'user_id', 'tenant_id')
            ->withPivot(['owner as owner', 'user_id as user_id', 'tenant_id as tenant_id']);
    }

    public function getFullNameAttribute()
    {
		return $this->last_name . ' ' . $this->name;
	}
}
