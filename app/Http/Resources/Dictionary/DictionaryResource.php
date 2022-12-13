<?php

namespace App\Http\Resources\Dictionary;

use App\Position;
use App\ProfileGroup;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DictionaryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'users' => DictionaryEmployeeResource::collection(User::query()->whereHas('description', function ($query){
                $query->where('is_trainee',0);
            })->get()),
            'positions' => DictionaryPositionResource::collection(Position::all()),
            'profile_groups' => DictionaryProfileGroupResource::collection(ProfileGroup::all()),
        ];
    }
}
