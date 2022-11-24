<?php

namespace App\Http\Requests\News;

use App\Models\News\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function getArticle(): Article
    {
        return Article::availableFor(Auth::user())->findOrFail($this->route('article_id'));
    }

    public function getArticleId(): ?string
    {
        return $this->route('article_id');
    }
}
