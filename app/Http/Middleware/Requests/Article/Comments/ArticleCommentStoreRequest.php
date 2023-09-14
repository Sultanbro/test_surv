<?php

namespace App\Http\Requests\Article\Comments;

use App\Entities\DataTransferObjects\News\CommentStoreDTO;
use App\Http\Requests\Article\ArticleRequest;
use Illuminate\Support\Facades\Auth;

class ArticleCommentStoreRequest extends ArticleRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:10000',],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id',],
        ];
    }

    public function getData(): CommentStoreDTO
    {
        $validated = parent::validated();

        return new CommentStoreDTO(
            $validated['content'],
            $validated['parent_id'],
            $this->getArticleId(),
            Auth::id(),
        );
    }
}
