<?php

namespace App\Http\Resources\Top;

use Illuminate\Http\Resources\Json\JsonResource;

class SwitchListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $switch = '';
        if (object_get($this, 'switch_utilist')){
            $switch = 'switch_utilist';
        }elseif (object_get($this, 'switch_rentability')){
            $switch = 'switch_rentability';
        }else{
            $switch = 'switch_proceeds';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'switch' => $this->{$switch}
        ];
    }
}
