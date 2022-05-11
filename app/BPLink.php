<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BPLink extends Model
{
    protected $connection= 'bpartners_db';

    protected $table = 'links';

    public $timestamps = false;
}
