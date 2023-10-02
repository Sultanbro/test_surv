<?php

namespace Eddir\Messenger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MessengerFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'thumbnail_path',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(MessengerMessage::class, 'message_id');
    }

    public function getFilePathAttribute($value): ?string
    {
        return $value ? Storage::disk('s3')->get(
            $value
        ) : null;
    }

    public function getThumbnailPathAttribute($value): ?string
    {
        return $value ? Storage::disk('s3')->get(
            $value
        ) : null;
    }
}
