<?php

namespace App\Http\Resources\News\Dictionary;

use App\Position;
use App\ProfileGroup;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DictionaryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'users' => DictionaryEmployeeResource::collection(User::all()),
            'positions' => DictionaryPositionResource::collection(Position::all()),
            'profile_groups' => DictionaryProfileGroupResource::collection(ProfileGroup::all()),
        ];
    }
}
