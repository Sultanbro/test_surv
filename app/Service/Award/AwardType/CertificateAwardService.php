<?php

namespace App\Service\Award\AwardType;

use App\Enums\AwardTypeEnum;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Models\Course;
use App\Models\CourseResult;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class CertificateAwardService implements AwardInterface
{
    public $path = 'awards';


    public function fetch(array $params): array
    {
        $user = User::query()->findOrFail($params['user_id']);
        $result = [];
        try {
            $type = AwardTypeEnum::TYPES[$params['key']];

            $result['my'] = Award::whereHas('courses', function ($query) use ($user) {
                $query->where('award_course.user_id', $user->id);
            })
                ->whereHas('category', function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->with([
                    'courseUsers' => function ($query) use($user) {
                        $query->select('users.id', 'users.name', 'users.last_name', 'users.avatar')
                            ->where('users.id', $user->id);
                        },
                    'category',
                ])
                ->get();

            $courseIds = collect(CourseResult::activeAwardCourses($user->id))->pluck('id')->toArray();
            $result['available']  = Course::whereDoesntHave('courseAwards', function ($query) use ($user){
                $query->where('award_course.user_id', $user->id);
            })->whereHas('award')
                ->whereIn('id', $courseIds)
                ->with('award', 'award.category')
                ->get();


            $result['other'] = Award::whereHas('courses', function ($query) use ($user) {
                $query->whereNot('award_course.user_id', $user->id);
            })
                ->whereHas('category', function ($query) use ($type) {
                    $query->where('type', $type)
                        ->where('hide', false);
                })
                ->with(['courseUsers' => function ($query) use($user) {
                    $query->select('users.id', 'users.name', 'users.last_name', 'users.avatar')
                        ->whereNot('users.id', $user->id);
                }, 'category'])
                ->get();



            return $this->mapAwardsData($result, $user);

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param StoreAwardRequest $request
     * @return Builder|Model
     * @throws Exception
     */
    public function store(StoreAwardRequest $request)
    {
        try {
            if (Award::query()
                ->where('award_category_id', $request->input('award_category_id'))->exists()) {
                return \response()->success('Категория сертификат уже имеет награду', 500, 'error');

            }
            if (!$request->has('course_ids')) {
                return \response()->success('Пожалуйста укажите курс', 500, 'error');

            }

            $file = $this->saveAwardFile($request);
            $preview = $this->saveAwardPreview($request);

            $success = Award::query()->create([
                'award_category_id' => $request->input('award_category_id'),
                'path' => $file['relative'],
                'format' => $file['format'],
                'preview_path' => $preview['relative'],
                'preview_format' => $preview['format'],
                'styles' => $request->input('styles'),
            ]);

            if ($success) {
                Course::whereIn('id', $request->input('course_ids'))
                    ->update(['award_id' => $success->id]);

            }
            return \response()->success($success);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param UpdateAwardRequest $request
     * @param Award $award
     * @return bool
     * @throws Exception
     */
    public function update(UpdateAwardRequest $request, Award $award)
    {
        try {
            $parameters = $request->except(['_method', 'course_ids']);
            if (FileHelper::checkFile($award->path)) {
                FileHelper::delete($award->path, $this->path);
            }

            if ($request->has('file')) {
                $file = $this->saveAwardFile($request);
                $parameters['format'] = $file['format'];
                $parameters['path'] = $file['relative'];
            }

            if ($request->has('preview')) {
                $preview = $this->saveAwardPreview($request);
                $parameters['preview_format'] = $preview['format'];
                $parameters['preview_path'] = $preview['relative'];
            }

            if ($request->has('course_ids')){
                $this->updateCourses($request->input('course_ids'), $award);

            }


            return $award->update($parameters);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $request
     * @return array
     * @throws BusinessLogicException
     */
    private function saveAwardFile($request): array
    {
        if (!$request->hasFile('file')) {
            return [
                'relative' => '',
                'format' =>  '',
                'temp' => ''
            ];
        }

        $file = $request->file('file');
        if (!$filename = FileHelper::save($file, $this->path)) {
            throw new BusinessLogicException(__('exception.save_error'));
        }
        return [
            'relative' => $filename,
            'format' => $file->getClientOriginalExtension(),
            'temp' => FileHelper::getUrl($this->path, $filename)
        ];
    }

    /**
     * @param $request
     * @return array
     * @throws BusinessLogicException
     */
    public function saveAwardPreview($request): array
    {
        if (!$request->hasFile('preview')) {
            return [
                'relative' => '',
                'format' =>  '',
                'temp' => ''
            ];
        }

        $preview = $request->file('preview');
        if (!$filename = FileHelper::save($preview, $this->path)) {
            throw new BusinessLogicException(__('exception.save_error'));
        }
        return [
            'relative' => $filename,
            'format' => $preview->getClientOriginalExtension(),
            'temp' => FileHelper::getUrl($this->path, $filename)
        ];
    }

    /**
     * @param $courseIds
     * @param $award
     * @return void
     */
    private function updateCourses($courseIds, $award)
    {
        $existingCourses = Course::where('award_id', $award->id)->get()->pluck('id');
        $removeCourses = $existingCourses->diff(
            $courseIds
        );

        if ($removeCourses) {
            Course::whereIn('id', $removeCourses)
                ->update(['award_id' => 0]);
        }
        Course::whereIn('id', $courseIds)
            ->update(['award_id' => $award->id]);
    }

    /**
     * @param array $data
     * @return array
     */
    private function mapAwardsData(array $data, User $user): array
    {
        $result = [];

        $myAwards = $data['my'];
        $availableAwards = $data['available'];
        $otherAwards = $data['other'];

        $myAwardsRead = $myAwards->isEmpty() || !$myAwards->contains(fn($a) => !$a->read);

        foreach ($myAwards as $item) {
            $category = $item->category;
            if (!isset($result[$category->id])) {
                $result[$category->id] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'type' => $category->type,
                ];
            }
            foreach ($item->courseUsers as $user) {
                $result[$category->id]['my'][] = [
                    'award_id' => $item->id,
                    'award_category_id' =>$category->id,
                    'format' => $user->pivot->format,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'user_id' => $user->id,
                    'course_id' => $user->pivot->course_id,
                    'previewPath' => FileHelper::getUrl($this->path, $user->pivot->preview_path),
                    'tempPath' => FileHelper::getUrl($this->path, $user->pivot->path),
                ];
            }

        }

        foreach ($availableAwards as $item) {
            $award = $item->award;
            $category = $award->category;
            if (!isset($result[$category->id])) {
                $result[$category->id] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'type' => $category->type,
                ];
            }
            $result[$category->id]['available'][] = [
                'award_id' => $award->id,
                'award_category_id' => $category->id,
                'format' => $award->format,
                'course_id' => $item->id,
                'course_name' => $item->name,
                'previewPath' => FileHelper::getUrl($this->path, $award->preview_path),
                'tempPath' => FileHelper::getUrl($this->path, $award->path),
            ];

        }
        foreach ($otherAwards as $item) {
            $category = $item->category;

            if (!isset($result[$category->id])) {
                $result[$category->id] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'type' => $category->type,
                ];
            }
            foreach ($item->courseUsers as $user) {
                $result[$category->id]['other'][] = [
                    'award_id' => $item->id,
                    'award_category_id' => $category->id,
                    'format' => $user->pivot->format,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'user_id' => $user->id,
                    'course_id' => $user->pivot->course_id,
                    'previewPath' => FileHelper::getUrl($this->path, $user->pivot->preview_path),
                    'tempPath' => FileHelper::getUrl($this->path, $user->pivot->path),
                ];
            }

        }

        return ['data' => array_values($result), 'read' => $myAwardsRead];
    }


}
