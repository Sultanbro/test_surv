<?php

namespace App\Repositories\CoursesV2;

use App\DTO\CoursesV2\AssignedCourseFilterPropsDto;
use App\Models\CourseV2 as Model;
use App\Traits\UploadFileS3;
use App\Repositories\CoreRepository;
use App\DTO\CoursesV2\CoursePropsDto;
use App\DTO\CoursesV2\CourseFilterPropsDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Класс для работы с Repository.
 */
class CourseV2Repository extends CoreRepository
{
    use UploadFileS3;

    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function filter(CourseFilterPropsDto $dto): LengthAwarePaginator
    {
        $query = $this->model()->query();

        if ($dto->search) $query->where('name', 'LIKE', '%' . $dto->search . '%');
        if ($dto->profile_group_id) $query->whereHas('groups', fn($q) => $q->where('id', $dto->profile_group_id));
        if ($dto->position_id) $query->whereHas('positions', fn($q) => $q->where('id', $dto->position_id));
        if ($dto->type) $query->where('type', $dto->type);
        if ($dto->for_sale) $query->where('for_sale', $dto->for_sale);
        if ($dto->created_date) $query->whereDate('created_at', $dto->created_date);

        return $query->paginate($dto->per_page ?? 10);
    }

    public function createCourse(CoursePropsDto $dto, $centralCourseId)
    {
        $icon = $this->uploadFile('/coursesV2', $dto->icon);
        $background = $this->uploadFile('/coursesV2', $dto->background);


        return $this->model()->query()->create([
            'name' => $dto->name,
            'short' => $dto->short,
            'desc' => $dto->desc,
            'icon' => $icon['relative'],
            'background' => $background['relative'],
            'elements' => $dto->elements,
            'type' => $dto->type,
            'targets' => $dto->targets,
            'passing_score' => $dto->passing_score,
            'attempts' => $dto->attempts,
            'mix_questions' => $dto->mix_questions,
            'show_answers' => $dto->show_answers,
            'start' => $dto->start,
            'stop' => $dto->stop,
            'curator_id' => $dto->curator_id,
            'curator_group_id' => $dto->curator_group_id,
            'curator_position_id' => $dto->curator_position_id,
//            'notifications' => $dto->notifications,
            'award_id' => $dto->award_id,
            'show_as_finished' => $dto->show_as_finished,
            'bonus' => $dto->bonus,
            'for_sale' => $dto->for_sale,
            'central_course_id' => $centralCourseId,
            'tenant_id' => tenant('id'),
        ]);
    }

    public function updateCourse(CoursePropsDto $dto)
    {
        $icon = $this->uploadFile('/coursesV2', $dto->icon);
        $background = $this->uploadFile('/coursesV2', $dto->background);


        return $this->model()
            ->query()
            ->where('id', $dto->id)
            ->create([
                'name' => $dto->name,
                'short' => $dto->short,
                'desc' => $dto->desc,
                'icon' => $icon['relative'],
                'background' => $background['relative'],
                'elements' => $dto->elements,
                'type' => $dto->type,
                'targets' => $dto->targets,
                'passing_score' => $dto->passing_score,
                'attempts' => $dto->attempts,
                'mix_questions' => $dto->mix_questions,
                'show_answers' => $dto->show_answers,
                'start' => $dto->start,
                'stop' => $dto->stop,
                'curator_id' => $dto->curator_id,
                'curator_group_id' => $dto->curator_group_id,
                'curator_position_id' => $dto->curator_position_id,
                'award_id' => $dto->award_id,
                'show_as_finished' => $dto->show_as_finished,
                'bonus' => $dto->bonus,
                'for_sale' => $dto->for_sale,
            ]);
    }

    public function delete(Model $course)
    {
        $course->delete();
        return true;
    }

    public function filterAssigned(AssignedCourseFilterPropsDto $dto)
    {
        $query = $this->model()->query()->whereHas('targets');

        if ($dto->search) $query->where('name', 'LIKE', '%' . $dto->search . '%');
        if ($dto->target) {
        }//$query->whereHas('groups', fn ($q) => $q->where('id', $dto->profile_group_id));
        if ($dto->curator_id) $query->whereHas('targetsPivot', fn($q) => $q->where('curator_id', $dto->curator_id));
        if ($dto->type) $query->where('type', $dto->type);
        if ($dto->created_date) $query->whereDate('created_at', $dto->created_date);
        if ($dto->stop_date) $query->whereDate('stop', $dto->stop_date);

        return $query->paginate($dto->per_page ?? 10);
    }
}
