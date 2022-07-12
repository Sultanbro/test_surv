<?php

namespace App\Models\Videos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Videos\Video;

class VideoPlaylist extends Model
{   
    use SoftDeletes;

    protected $table = 'video_playlists';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'category_id',
        'text',
        'img',
    ];


    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }

    public function videos()
    {
        return $this->hasMany('App\Models\Videos\Video', 'playlist_id', 'id')
            ->with('questions')
            ->with('item_model', function ($query){
                $query->where('type', 2);
            })
            ->orderBy('order', 'asc');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Videos\VideoGroup', 'category_id', 'id')->where('parent_id', 0)->with('videos', 'children'); 
    }
    
    public function category()
    {
        return $this->belongsTo('App\Models\Videos\VideoCategory', 'category_id', 'id');
    }

    public function poster()
    {
        $video = Video::where('playlist_id', $this->id)->first();
        return $video ? $video->poster : '';
    }
}
