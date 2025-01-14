<?php

namespace App\DTO\Structure;

use Illuminate\Foundation\Http\FormRequest;

class StructureCardUpdateDTO extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules():array
    {
        return [
            'name' => 'required_without:group_id|string',
            'group_id' => 'required_without:name|exists:profile_groups,id',
            'parent_id' => 'nullable|exists:structure_card,id',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'position_id' => 'nullable|exists:position,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'boolean',
            'is_group' => 'boolean',
            'is_vacant' => 'boolean'
        ];
    }
}