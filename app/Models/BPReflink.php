<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BPReflink extends Model
{
    protected $connection= 'bpartners_db';

    protected $table = 'reflinks';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'info',
    ];
}
