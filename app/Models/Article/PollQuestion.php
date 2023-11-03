<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'multi_answer',
        'question'
    ];

    public function answers()
    {
        return $this->hasMany(PollAnswer::class, 'question_id');
    }
}
