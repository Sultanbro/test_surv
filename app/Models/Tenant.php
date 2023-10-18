<?php

namespace App\Models;

use App\Models\Portal\Portal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory;

    protected $fillable = [
        'id',
        'global_id',
        'data'
    ];

    /**
     * @param string $id
     * @return Builder
     */
    public static function getById(string $id): Builder
    {
        return self::query()->where('id', $id);
    }

    /**
     * @return HasOne
     */
    public function portal(): HasOne //TODO Portal refactor: replace HasOne with HasMany
    {
        return $this->hasOne(Portal::class, 'tenant_id', 'id');
    }
}