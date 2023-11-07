<?php

namespace App\Http\Requests\Article;

use App\Entities\DataTransferObjects\News\ArticleStoreDTO;
use App\Enums\ArticleAvailableForTypeEnum;
use App\Rules\IsFileAlreadyUsed;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ArticleVoteRequest extends ArticleRequest
{

    public function rules(): array
    {
        return [
            'votes' => ['required', 'array'],
            'votes.*' => ['required', 'array'],
            'votes.*.question_id' => ['required', 'int'],
            'votes.*.answers_ids' => ['required', 'array']
        ];
    }
}
