<?php

namespace App\Models;

use App\Models\Award\Award;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
