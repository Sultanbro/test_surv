<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{   
    protected $fillable = ['user_id', 'title', 'message', 'type', 'image', 'email', 'total_sent'];

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function formattedDate() {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
