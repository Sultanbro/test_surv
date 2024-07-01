<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingTime extends Model
{
    public $timestamps = true;

    protected $table = 'working_times';

    protected $fillable = [
        'name',
        'time'
    ];


    /**
     * Связь с моделью user
     *
     * @return Eloquent
     */
    public function user()
    {
      return $this->hasOne('App\User');
    }
}
