<?php

namespace App\Models\Comment;

use App\Models\Article\Article;
use App\Models\Like\Like;
use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property int $parent_id
 * @property int $user_id
 * @property int $article_id
 * @property string $content
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Comment $parent
 * @property-read Article $article
 * @property-read User $author
 * @property-read Like[] $likes
 * @property-read CommentReaction[] $reactions
 *
 * @mixin Eloquent
 */
class Comment extends Model
{
    use SoftDeletes,
        NodeTrait;

    protected $fillable = [
        'parent_id',
        'user_id',
        'article_id',
        'content'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable', 'likeable_type', 'likeable_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class, 'comment_id', 'id');
    }
}
