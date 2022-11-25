<?php

namespace App\Models\Article;

use App\Enums\ArticleAvailableForTypeEnum;
use App\Filters\QueryFilter;
use App\Models\Comment\Comment;
use App\Models\File\File;
use App\Models\News\Like;
use App\Traits\Filterable;
use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property array|null $available_for
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read User $author
 * @property-read Comment $comments
 * @property-read MorphMany $likes
 * @property-read File $files
 * @property-read User $views
 * @property-read User $favourites
 * @property-read User $pins
 *
 * @method static Builder|Article availableFor(User $user)
 * @method static Builder|Article filter(QueryFilter $filter)
 *
 * @mixin Eloquent
 */
class Article extends Model
{
    use SoftDeletes, Filterable;

    protected $fillable = [
        'author_id',
        'title',
        'content',
        'available_for',
    ];

    protected $casts = [
        'available_for' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'article_id', 'id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(
            Like::class,
            'likeable',
            'likeable_type',
            'likeable_id'
        );
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable', 'fileable_type', 'fileable_id');
    }

    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_views_users', 'article_id', 'user_id');
    }

    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_favourites_users', 'article_id', 'user_id');
    }

    public function pins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_pins_users', 'article_id', 'user_id');
    }

    public static function scopeAvailableFor(Builder $builder, User $user): Builder
    {
        return $builder->where(function (Builder $query) use ($user) {
            $query->where('author_id', $user->id)
                ->orWhereNull('available_for')
                ->orWhereJsonContains('available_for', ['id' => $user->id, 'type' => ArticleAvailableForTypeEnum::EMPLOYEE])
                ->orWhereJsonContains('available_for', ['id' => $user->position_id, 'type' => ArticleAvailableForTypeEnum::POSITION]);

            $profileGroups = $user->groups;
            foreach ($profileGroups as $id) {
                $query->orWhereJsonContains('available_for', ['id' => $id, 'type' => ArticleAvailableForTypeEnum::PROFILE_GROUP]);
            }
        });
    }
}
