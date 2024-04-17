<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $user_id
 * @property int $author_id
 * @property string $author
 * @property string $date
 * @property string $description
 * @property string $payload
 */
class TimetrackingHistory extends Model
{
    use SoftDeletes;

    protected $table = 'timetracking_history';

    protected $fillable = [
        'user_id',
        'author_id',
        'author',
        'date',
        'description',
        'payload'
    ];

    protected $casts = [
        'data' => 'payload'
    ];
}
