<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $table = 'user_reports';

  
    public $timestamps = true;

    protected $fillable = [
        'user_id',
		'date',
        'title',
        'text'
    ];

}
