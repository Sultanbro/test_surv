<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseItem;

class TenantUser extends Model
{
    protected $table = 'tenant_user';

    protected $connection = 'mysql';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'img',
    ];


    public function users()
    {
        return $this->belongsToMany('App\Models\CentralUser');
    }
}
