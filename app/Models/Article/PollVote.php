<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'question_id',
        'answer_id',
        'user_id'
    ];

    protected $casts = [
        'answer_ids' => 'array'
    ];
}
