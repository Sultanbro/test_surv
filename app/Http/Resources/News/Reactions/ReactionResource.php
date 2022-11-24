<?php

namespace App\Http\Resources\News\Reactions;

use App\Models\News\CommentReaction;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property CommentReaction $resource
 */
class ReactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [];
    }
}
