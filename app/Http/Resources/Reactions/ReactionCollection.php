<?php

namespace App\Http\Resources\Reactions;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property Collection $collection
 */
class ReactionCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        $this->collection = $this->collection->groupBy('reaction');

        $result = [];

        foreach ($this->collection as $reactionCode => $value) {

            $users = collect();
            foreach ($value as $item) {
                $users->push($item->user);
            }

            $result[] = [
                'reaction' => $reactionCode,
                'reaction_count' => $users->count(),
                'is_reacted' => (bool)$users->where('id', Auth::id())->count(),
                'users' => UserResource::collection($users)->toArray($request)
            ];
        }

        return ['reactions' => $result];
    }
}
