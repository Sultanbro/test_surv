<?php

namespace App\Models\Videos;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{   
    protected $table = 'video_comments';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'video_id',
        'text',
    ];

}
