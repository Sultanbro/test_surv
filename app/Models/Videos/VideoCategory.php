<?php

namespace App\Models\Videos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCategory extends Model
{   
    use SoftDeletes;

    protected $table = 'video_categories';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function playlists()
    {
        return $this->hasMany('App\Models\Videos\VideoPlaylist', 'category_id', 'id');
    }
}
