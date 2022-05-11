<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserNotification extends Model
{   
	protected $table = 'user_notifications';

    protected $fillable = ['user_id', 'title', 'message', 'note', 'read_at', 'group', 'about_id']; // Maybe should add 'type' field in future

    public $timestamps = true;

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'ID');
    }

    public function formattedDate() {
        return Carbon::parse($this->created_at)->addHours(6)->format('d.m.Y H:i:s');
    }

    public function formattedReadAt() {
        if($this->read_at) {
            return Carbon::parse($this->read_at)->addHours(6)->format('d.m.Y H:i:s');
        } else {
            return '';
        }
        
    }

}
