<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadNotification extends Model
{   
    protected $fillable = ['notification_id', 'user_id'];

    public function notification() {
        return $this->belongsTo('App\Notification', 'notification_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
