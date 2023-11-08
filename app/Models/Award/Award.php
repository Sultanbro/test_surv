<?php

namespace App\Models\Award;

use App\Helpers\FileHelper;
use App\Models\Course;
use App\Observers\AwardObserver;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'format',
        'path',
        'preview_format',
        'preview_path',
        'styles',
        'award_category_id',
        'targetable_type',
        'targetable_id',
        'type',
        'read',
    ];
    protected $appends = ['tempPath'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $dispatchesEvents = [
        'deleted' => AwardObserver::class
    ];

    const TYPE_PUBLIC = 'public';

    const TYPE_PERSONAL = 'personal';

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AwardCategory::class, 'award_category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'award_course', 'award_id', 'course_id')
            ->withPivot(['user_id', 'path', 'format'])->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function courseUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'award_course', 'award_id', 'user_id')
            ->withPivot(['course_id', 'path', 'format','preview_path','preview_format'])->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'award_user', 'award_id','user_id')
            ->withPivot(['path', 'format','preview_path','preview_format'])
            ->withTimestamps();

    }

    public function getTempPathAttribute(){
        if ($this->path != ''){
           return FileHelper::getUrl('awards', $this->path);
        }
        return $this->path;

    }

}
