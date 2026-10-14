<?php

namespace App\Models\Article;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'question_id',
        'answer',
        'order'
    ];

    public function poll()
    {
        return $this->belongsTo(Article::class);
    }

    public function question()
    {
        return $this->belongsTo(PollQuestion::class, 'question_id');
    }

    public function votes()
    {
        return $this->belongsToMany(User::class, 'poll_votes', 'answer_id', 'user_id')->withTrashed();
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($answer) {
            $answer->votes()->delete();
        });
    }
}
