<?php

namespace App\Http\Resources\CoursesV2;

use App\Models\CentralCourse;
use App\Models\CourseV2;
use App\Traits\UploadFileS3;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCourseProgramResource extends JsonResource
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

        return [
            'id' => $this->id,
            'name' => $this->name,
            ''
        ];
    }
}
