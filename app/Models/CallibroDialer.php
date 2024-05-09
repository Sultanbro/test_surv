<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallibroDialer extends Model
{
    use HasFactory;

    protected $table = 'callibro_dialers';

    public $timestamps = false;

    protected $fillable = [
        'group_id', 
        'dialer_id', 
        'script_id', // for grades 
        'talk_hours',
        'talk_minutes', 
    ];

}
