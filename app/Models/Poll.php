<?php

namespace App\Models;

use App\Models\File\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use HasFactory, SoftDeletes;

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

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable', 'fileable_type', 'fileable_id');
    }
}
