<?php

namespace App\Models\Article;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $article_id
 * @property int $multi_answer
 * @property string $question
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property PollAnswer $answers
 *
 * @mixin Eloquent
 */
class PollQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'multi_answer',
        'question',
        'order'
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(PollAnswer::class, 'question_id');
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($question) {
            $question->answers()->each(function ($answer) {
                $answer->delete();
            });
        });
    }
}
