<?php

namespace App\Http\Resources\Articles\Comments;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCommentCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        $commentsCount = $this->resource->count();
        foreach ($this->resource as $comment) {
            $commentsCount += $comment->children->count();
        }

        return [
            'comments' => ArticleCommentResource::collection($this->resource)->toArray($request),
            'comments_count' => $commentsCount,
        ];
    }
}
