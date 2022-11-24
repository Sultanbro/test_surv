<?php

namespace App\Models\News;

use App\Helpers\FileHelper;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $fileable_id
 * @property string $fileable_type
 *
 * @property string $original_name
 * @property string $local_name
 * @property string $extension
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read string $url
 * @property-read string $path
 *
 * @mixin Eloquent
 */
class File extends Model
{
    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'original_name',
        'local_name',
        'extension',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return FileHelper::getUrl(config('app.file.path'), $this->local_name);
    }

    public function getPathAttribute(): string
    {
        return FileHelper::getPath(config('app.file.path'), $this->local_name);
    }
}
