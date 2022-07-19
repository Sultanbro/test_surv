<?php

namespace App\Models\Videos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Videos\Video;
use App\Contracts\CourseInterface;

class VideoPlaylist extends Model implements CourseInterface
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

    /**
     * CourseInterface
     * @param mixed $items
     * 
     * @return [type]
     */
    public function pluckVideos($items) {
        $arr = [];

        foreach ($items as $key => $item) {
            if($item->videos) $arr = array_merge($arr, $item->videos->pluck('id')->toArray());
           
            if($item->children) $arr = array_merge($arr, $this->pluckVideos($item->children));
        }

        return $arr;
    }

    /**
     * CourseInterface
     * @return [type]
     */
    public function getOrder()
    {
        $pl = self::with('groups')->find($this->id);

        $arr = Video::where('group_id', 0)
            ->where('playlist_id', $pl->id)
            ->get()
            ->pluck('id')
            ->toArray();
        
        return array_merge($arr, $this->pluckVideos($pl->videos));
    }

    /**
     * CourseInterface
     * @param mixed $id
     * 
     * @return [type]
     */
    public function nextElement($id)
    {
        $arr = $this->getOrder();
        $key = array_search($id, $arr);
        return $key && $key + 1 <= count($arr) - 1 ? $arr[$key + 1] : null;
    }
    
}
