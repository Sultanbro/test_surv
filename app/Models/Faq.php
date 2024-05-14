<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory, Model, SoftDeletes,
    Relations\BelongsTo, Relations\HasMany
};

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'faqs';

    protected $fillable = ['parent_id', 'title', 'page', 'body', 'order'];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
