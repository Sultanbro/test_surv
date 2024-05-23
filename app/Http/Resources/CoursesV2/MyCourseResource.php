<?php

namespace App\Http\Resources\CoursesV2;

use App\Models\CourseV2;
use App\Traits\UploadFileS3;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCourseResource extends JsonResource
{
    use UploadFileS3;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /**
         * @var $this CourseV2
         */

        $this->load('itemsPivot.model');

        foreach ($this->itemsPivot as $item) {
            $this->itemsPivot->all_stages = $item->model->countAllStages();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'short' => $this->short,
            'desc' => $this->desc,
            'icon' => $this->getFile($this->icon),
            'background' => $this->getFile($this->background),
            'program' => $this->itemsPivot//MyCourseProgramResource::collection($this->itemsPivot)
        ];
    }
}
