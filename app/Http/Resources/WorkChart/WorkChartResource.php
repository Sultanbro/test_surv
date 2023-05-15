<?php

namespace App\Http\Resources\WorkChart;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'text_name' => $this->text_name,
            'work_charts_type' => $this->workChartType,
            'created_at' => $this->created_at,
        ];
    }
}
