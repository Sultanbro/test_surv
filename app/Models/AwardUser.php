<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AwardUser extends Pivot
{
    protected $table = 'award_user';

    public $timestamps = false;

    protected $fillable = [
        'award_id',
        'user_id',
    ];
    /**
     * relation to user_id
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    /**
     * relation to award_id
     */
    public function award()
    {
        return $this->belongsTo('App\Models\Award', 'award_id', 'id');
    }

}
