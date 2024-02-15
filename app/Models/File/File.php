<?php

namespace App\Models\File;

use App\Helpers\FileHelper;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property string $url
 * @property-read string $path
 *
 * @mixin Eloquent
 */
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'original_name',
        'local_name',
        'extension',
        'url'
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'user_signed_file',
            'file_id',
            'user_id'
        );
    }
}
