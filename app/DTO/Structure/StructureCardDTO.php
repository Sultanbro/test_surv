<?php

namespace App\DTO\Structure;


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StructureCardDTO
{
    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public static function validate(Request $request): array
    {
        $validator = Validator::make($request->all(), self::rules());

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'errors' => $validator->errors()
            ], 422));
        }

        return $validator->validated();
    }

    /**
     * @return string[]
     */
    public static function rules():array
    {
        return [
            'name' => 'required_without:group_id|string',
            'group_id' => 'required_without:name|nullable|exists:profile_groups,id',
            'parent_id' => 'nullable|exists:structure_card,id',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'position_id' => 'nullable|exists:position,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'boolean',
            'is_group' =>'boolean',
            'is_vacant' =>'boolean'
        ];
    }
}