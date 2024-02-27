<?php

namespace App\Http\Resources\KB;

use App\KnowBase;
use Illuminate\Http\Resources\Json\JsonResource;

class KnowBaseTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /**
         * @var $this KnowBase
         */
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'is_category' => $this->is_category,
            'order' => $this->order,
            'title' => $this->title,
//            'can_view' => $this->can_view,
//            'can_edit' => $this->can_edit,
            'children' => KnowBaseTreeResource::collection($this->onlyChildren)
        ];
    }
}
