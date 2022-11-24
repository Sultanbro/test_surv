<?php

namespace App\Http\Requests\News\Comments;

use App\Http\Requests\News\Comments\ArticleCommentRequest;

class ArticleCommentReactionRequest extends ArticleCommentRequest
{
    public function rules(): array
    {
        return [
            'reaction' => ['required', 'string', 'regex:/&{1}#{1}\d{2,6};{1}/'],
        ];
    }

    public function getReaction(): string
    {
        return parent::validated('reaction');
    }
}
