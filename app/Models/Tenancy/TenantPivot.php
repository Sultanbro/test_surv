<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class TenantPivot extends Model
{
    protected $connection = 'mysql';
    protected $table = 'tenant_pivot';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'owner',
    ];
}
