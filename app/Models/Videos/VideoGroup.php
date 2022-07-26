<?php

namespace App\Models\Videos;

use Illuminate\Database\Eloquent\Model;

class VideoGroup extends Model
{   
    protected $table = 'video_groups';

    public $timestamps = true;

    protected $appends = ['opened'];

    protected $fillable = [
        'title',
        'parent_id',
        'category_id' // Должно быть playlist_id, спутал в миграции. Исправьте пожалуйста
    ];

    public function getOpenedAttribute()
    {
        return false;
    }

    public function videos()
    { 
        return $this->hasMany('App\Models\Videos\Video', 'group_id', 'id')
            ->orderBy('order', 'asc')
            ->with('questions');
    }

    public function playlist() 
    {
        return $this->belongsTo('App\Models\Videos\VideoPlaylist', 'category_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children','videos');
    }

    public function parent() 
    {
        return $this->belongsTo('App\Models\Videos\VideoGroup', 'parent_id', 'id');
    }
}
