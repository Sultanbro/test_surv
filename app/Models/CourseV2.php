<?php

namespace App\Models;

use App\User;
use App\Position;
use App\ProfileGroup;
use App\Models\Award\Award;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
 */
class CourseV2 extends Model
{
    use HasFactory;

    protected $table = 'courses_v2';

    public const AUTOMATIC_TYPE = 1;
    public const INDIVIDUAL_TYPE = 2;

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

    public function users()
    {
        return $this->morphedByMany(User::class, 'target', 'course_targets_v2', 'course_id')
            ->withPivot('target_id', 'target_type');
    }

    public function positions()
    {
        return $this->morphedByMany(Position::class, 'target', 'course_targets_v2', 'course_id');
    }

    public function groups()
    {
        return $this->morphedByMany(ProfileGroup::class, 'course_target');
    }

    public function centralCourse()
    {
        return $this->belongsTo(CentralCourse::class);
    }
}
