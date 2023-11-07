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
    public function prepareForValidation()
    {
        if ($this->input('votes') != null) {
            $votesArray = json_decode($this->input('votes'), 1);
        } else {
            $votesArray = [];
        }

        $this->merge([
            'votes' => $votesArray
        ]);
    }

    public function rules(): array
    {
        return [
            'votes' => ['required', 'array'],
            'votes.*' => ['required', 'array'],
            'votes.*.question_id' => ['required', 'int'],
            'votes.*.answer_id' => ['required', 'array']
        ];
    }
}
