<?php

namespace App\Http\Resources\CoursesV2;

use App\Models\CentralCourse;
use App\Traits\UploadFileS3;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryCourseResource extends JsonResource
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
         * @var $this CentralCourse
         */
        $this->load('tenantCourse', 'tenantCourseItems', 'tenantCourseGrades.user');

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->tenantCourse->name,
            'short' => $this->tenantCourse->short,
            'desc' => $this->tenantCourse->desc,
            'background' => $this->getFile($this->tenantCourse->background),
            'slides' => $this->slides,
            'program' => $this->tenantCourseItems->get(['title', 'order', 'duration']),
            'grades' => $this->tenantCourseGrades
        ];
    }
}
