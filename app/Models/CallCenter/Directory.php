<?php

namespace App\Models\CallCenter;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{   
    protected $table = 'directory';
    protected $connection = 'call_center';

    public $timestamps = false;

    protected $fillable = [
        'account', // varchar 30
        'password', // varchar 30
        'domain', // voip.cfpsa.ru
        'context',  // voip.cfpsa.ru_context
        'toll_allow', // 600
        'provider', // 600
        'state', // active
    ];

}
