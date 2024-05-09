<?php

namespace App\Models\Comment;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $reaction
 * @property int $comment_id
 *
 * @property User $user
 *
 * @mixin Eloquent
 */
class CommentReaction extends Model
{
    protected $fillable = [
        'user_id',
        'comment_id',
        'reaction',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
