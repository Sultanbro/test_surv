<?php

namespace App\Http\Requests\Article;

use App\Traits\Paginateable;
use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    use Paginateable;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->defaultPage = config('app.pagination.articles.page');
        $this->defaultPerPage = config('app.pagination.articles.per_page');
    }

    public function rules(): array
    {
        return $this->withPaginationRules([
        ]);
    }
}
