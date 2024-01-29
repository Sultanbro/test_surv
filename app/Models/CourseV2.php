<?php

namespace App\Models;

use App\{User, Position, ProfileGroup, KnowBase};
use App\Models\{Books\Book, Award\Award, Videos\VideoPlaylist};
use Illuminate\Database\Eloquent\{Model,
    Collection,
    Relations\BelongsTo,
    Factories\HasFactory,
    Relations\MorphToMany,
    SoftDeletes};

/**
 * @property int $id
 * @property string $name
 * @property string $short
 * @property string $desc
 * @property string $icon
 * @property string $background
 * @property int $cat_id
 * @property int $price
 * @property int $for_sale
 * @property int $central_course_id
 * @property Collection $targets
 */
class CourseV2 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courses_v2';

    public const AUTOMATIC_TYPE = 1;
    public const INDIVIDUAL_TYPE = 2;
    public const PURCHASED_TYPE = 3;

    public const USER_TARGET = 1;
    public const POSITION_TARGET = 2;
    public const GROUP_TARGET = 3;

    public const TARGET_TYPES = [self::USER_TARGET, self::POSITION_TARGET, self::GROUP_TARGET];

    protected $fillable = [
        'name',
        'short',
        'desc',
        'icon',
        'background',
        'type',
        'passing_score',
        'attempts',
        'mix_questions',
        'show_answers',
        'start',
        'stop',
        'curator_id',
        'curator_group_id',
        'curator_position_id',
        'award_id',
        'show_as_finished',
        'bonus',
        'for_sale',
        'central_course_id',
        'tenant_id',
        'price',
    ];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'target', 'course_targets_v2', 'course_id')
            ->withPivot('target_id', 'target_type');
    }

    public function positions(): MorphToMany
    {
        return $this->morphedByMany(Position::class, 'target', 'course_targets_v2', 'course_id');
    }

    public function groups(): MorphToMany
    {
        return $this->morphedByMany(ProfileGroup::class, 'target', 'course_targets_v2', 'course_id');
    }

    public function targets(): Collection
    {
        $users = $this->users()->get();
        $positions = $this->positions()->get();
        $groups = $this->groups()->get();
        return $groups->merge($positions)->merge($users);
    }

    public function targetsPivot()
    {
        return $this->hasMany(CourseTargetV2::class, 'course_id');
    }

    public function books(): MorphToMany
    {
        return $this->morphedByMany(Book::class, 'item', 'course_items_v2', 'course_id')
            ->withPivot('item_id', 'item_type');
    }

    public function videos(): MorphToMany
    {
        return $this->morphedByMany(VideoPlaylist::class, 'item', 'course_items_v2', 'course_id');
    }

    public function kbs(): MorphToMany
    {
        return $this->morphedByMany(KnowBase::class, 'item', 'course_items_v2', 'course_id');
    }

    public function items(): Collection
    {
        $books = $this->books()->get();
        $videos = $this->videos()->get();
        $kbs = $this->kbs()->get();
        return $books->merge($videos)->merge($kbs);
    }

    public function centralCourse(): BelongsTo
    {
        return $this->belongsTo(CentralCourse::class);
    }
}
