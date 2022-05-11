<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BpartnersBooksPass extends Model
{
    protected $connection= 'bpartners_db';

    protected $table = 'passwords';
    public $timestamps = false;
}
