<?php

namespace App\Models\Videos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{   
    use SoftDeletes;

    protected $table = 'videos';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'links',
        'duration',
        'views',
        'playlist_id',
        'group_id',
        'order',
        'domain',
        'pass_grade'
    ];

    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }
    
    public function item_model()
    {
        return $this->hasOne('App\Models\CourseItemModel', 'item_id');
    }

    
    public function comments()
    {
        return $this->hasMany('App\Models\Videos\VideoComment');
    }

    public function playlist()
    {
        return $this->belongsTo('App\Models\Videos\VideoPlaylist');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Videos\VideoGroup');
    }
}
