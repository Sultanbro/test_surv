<?php

namespace App\Http\Requests\Article;

use App\Entities\DataTransferObjects\News\ArticleStoreDTO;
use App\Enums\ArticleAvailableForTypeEnum;
use App\Rules\IsFileAlreadyUsed;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ArticleStoreRequest extends ArticleRequest
{
    protected ?array $availableFor = null;

    public function prepareForValidation()
    {
        if ($this->input('files') != null) {
            $filesArray = json_decode($this->input('files'));
        } else {
            $filesArray = [];
        }

        if ($this->input('available_for') != null) {
            $availableObj = json_decode($this->input('available_for'));
            $availableArray = [];

            foreach ($availableObj as $item) {
                $availableArray[] = json_decode(json_encode($item), true);
            }
        } else {
            $availableArray = null;
        }

        $this->merge([
            'files' => $filesArray,
            'available_for' => $availableArray,
        ]);
    }

    public function rules(): array
    {
        $article = is_null($this->getArticleId())
            ? null
            : $this->getArticle();

        return [
            'title' => ['required', 'string', 'max:125',],
            'content' => ['required', 'string', 'max:10000',],

            # Poll attributes
            'questions' => ['nullable', 'array'],
            'questions.*' => ['required', 'array'],
            'questions.*.multi_answer' => ['required', 'bool'],
            'questions.*.question' => ['required', 'string'],
            'questions.*.order' => ['required', 'int'],

            'questions.*.answers' => ['required', 'array'],
            'questions.*.answers.*' => ['required', 'array'],
            'questions.*.answers.*.answer' => ['required', 'string'],
            'questions.*.answers.*.order' => ['required', 'int'],
            #

            'available_for' => ['nullable', 'array'],
            'available_for.*' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $id = $value['id'] ?? null;
                    $type = $value['type'] ?? null;

                    if (in_array($type, array_keys(ArticleAvailableForTypeEnum::CLASSES))) {

                        $model = (new (ArticleAvailableForTypeEnum::CLASSES[$type]))->find($id);

                        if (is_null($model)) {
                            return $fail('validation.article_available_for_wrong_data');
                        }

                        if (is_null($this->availableFor)) {
                            $this->availableFor = [];
                        }

                        $this->availableFor[] = [
                            'id' => $id,
                            'type' => $type,
                            'name' => $model instanceof User ? $model->full_name : $model->name,
                        ];

                        return null;
                    }

                    return $fail(__('validation.article_available_for_wrong_data'));
                }
            ],
            'available_for.*.id' => ['required', 'integer'],
            'available_for.*.type' => ['required', 'integer', Rule::in(array_keys(ArticleAvailableForTypeEnum::TYPES)),],

            'files' => ['present', 'array'],
            'files.*' => [
                'required',
                'integer',
                is_null($article)
                    ? (new IsFileAlreadyUsed())
                    : (new IsFileAlreadyUsed($article->getMorphClass(), $article->id))
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.article.title'),
            'content' => __('validation.attributes.article.content'),
            'available_for' => __('validation.attributes.article.available_for'),
            'files' => __('validation.attributes.article.files'),
        ];
    }

    public function getData(): ArticleStoreDTO
    {
        $validated = parent::validated();

        return new ArticleStoreDTO(
            Auth::id(),
            $validated['title'],
            $validated['content'],
            $this->availableFor,
            $validated['files'],
        );
    }
}
