<?php

namespace App\Http\Resources\Reactions;

use App\Models\Comment\CommentReaction;
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
