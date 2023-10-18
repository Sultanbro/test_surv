<?php

namespace App\Models\Article;

use App\Enums\ArticleAvailableForTypeEnum;
use App\Models\Comment\Comment;
use App\Models\File\File;
use App\Models\Like\Like;
use App\Traits\Filterable;
use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    protected $appends = [
        'content',
    ];

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
        return $this->belongsTo(User::class, 'author_id', 'id')->withTrashed();
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

    public function getContentAttribute()
    {
        $content = $this->attributes['content'];

        preg_match_all('/<img[^>]+>/i', $content, $result);

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>' .$content);
        $imageTags = $doc->getElementsByTagName('img');

        foreach($imageTags as $tag) {
            $url = $tag->getAttribute('src');
            $host = parse_url($url, PHP_URL_HOST);
            $path = parse_url($url, PHP_URL_PATH);

            if($host !== 'storage.oblako.kz') continue;
            if($path == '') continue;

            $path = str_replace('/tenant'.tenant('id'), '', $path);

            $tempUrl = \Storage::disk('s3')->temporaryUrl(
                $path, now()->addMinutes(360)
            );

            $tag->setAttribute('src', $tempUrl);

            //$content = str_replace(utf8_decode($url), $tempUrl, $content);
        }

        return $doc->saveHTML();
    }

    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_views_users', 'article_id', 'user_id')->withTrashed();
    }

    /**
     * @param int $userId
     * @return int
     */
    public static function countUnviewed(int $userId): int
    {
        $user = User::getAuthUser($userId);

        return Article::query()
            ->leftJoin(
                'article_views_users as views',
                function($join) use ($userId) {
                    $join->on('articles.id', '=', 'views.article_id');
                    $join->on('views.user_id', '=', DB::raw($userId));
                },
            )
            ->whereNull('views.user_id')
            ->where('created_at', '>', $user->created_at)
            ->count();
    }

    /**
     * @param int $userId
     * @return Builder[]|Collection
     */
    public static function getUnviewedArticle(int $userId):Collection|array
    {
        $user = User::getAuthUser($userId);

        return Article::query()
            ->leftJoin(
                'article_views_users as views',
                function($join) use ($userId) {
                    $join->on('articles.id', '=', 'views.article_id');
                    $join->on('views.user_id', '=', DB::raw($userId));
                },
            )
            ->whereNull('views.user_id')
            ->where('created_at', '>', $user->created_at)
            ->get();
    }

    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_favourites_users', 'article_id', 'user_id')->withTrashed();
    }

    public function pins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_pins_users', 'article_id', 'user_id')->withTrashed();
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
