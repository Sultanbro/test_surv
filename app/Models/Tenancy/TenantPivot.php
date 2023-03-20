<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

//TODO Portal refactor: replace by 'portal__user'
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
