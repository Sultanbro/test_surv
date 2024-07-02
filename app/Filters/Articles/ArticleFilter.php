<?php

namespace App\Filters\Articles;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ArticleFilter extends QueryFilter
{
    protected array $booleanFields = ['is_favourite'];

    public function q(string $search): Builder
    {
        return $this->builder->where(function (Builder $query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhereRaw('regexp_replace(content, "<[^>]*>", "") like ?', '%' . $search . '%');
        });
    }

    public function postId(string $postId): Builder
    {
        return $this->builder->where('id', $postId);
    }

    public function startDate(string $startDate): Builder
    {
        return $this->builder->where('created_at', '>=', Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s'));
    }

    public function endDate(string $endDate): Builder
    {
        return $this->builder->where('created_at', '<=', Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s'));
    }

    public function authorId(string $authorId): Builder
    {
        return $this->builder->where('author_id', $authorId);
    }

    public function isFavourite(bool $isFavourite): Builder
    {
        return $isFavourite
            ? $this->builder->whereHas('favourites', fn($q) => $q->where('user_id', Auth::id()))
            : $this->builder;
    }
}
