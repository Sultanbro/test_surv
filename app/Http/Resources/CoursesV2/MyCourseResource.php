<?php

namespace App\Http\Resources\CoursesV2;

use App\Models\CentralCourse;
use App\Models\CourseV2;
<<<<<<< HEAD
=======
use App\Service\CourseV2\MyCourseV2Service;
>>>>>>> dev
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
<<<<<<< HEAD
=======
        $this->load('itemsPivot.model');

        foreach ($this->itemsPivot as $item) {
            $this->itemsPivot->all_stages = $item->model->countAllStages();
        }
>>>>>>> dev

        return [
            'id' => $this->id,
            'name' => $this->name,
            'short' => $this->short,
            'desc' => $this->desc,
            'icon' => $this->getFile($this->icon),
            'background' => $this->getFile($this->background),
<<<<<<< HEAD
            'program' => $this->itemsPivot
=======
            'program' => $this->itemsPivot//MyCourseProgramResource::collection($this->itemsPivot)
>>>>>>> dev
        ];
    }
}