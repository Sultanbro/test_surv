<?php

namespace App\Service\Awards\AwardTypes;

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

            //award category
            //award
            //course ? award_id
            //course result
//            CourseResult::query()
//                ->where('user_id', $user->id)
//                ->whereNotNull('ended_at')
//                ->with('course', function ($q) use ($item){
//                    $q->where('award_id', $item['award_id']);
//                })
//                ->get()
//                ->pluck('course');
            $result['my'] = AwardCategory::query()
                ->where('type', $type)
                ->with('awards', function ($query) use ($user) {
                    $query->with('users');
                    $query->whereHas('users', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    });
                })
                ->get();

            $result['available'] = AwardCategory::query()
                ->where('type', $type)
                ->with('awards', function ($query) use ($user) {
                    $query->whereDoesntHave('users', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    });
                })
                ->get();

            $result['other'] = AwardCategory::query()
                ->where('type', $type)
                ->with('awards', function ($query) use ($user) {
                    $query->with('users', function ($q) {
                        $q->select('id', 'name', 'last_name', 'avatar');
                    })
                        ->whereHas('users', function ($q) use ($user) {
                            $q->whereNot('id', $user->id);
                        });
                })
                ->where('hide', false)
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
                throw new BusinessLogicException('certificate category already has award');
            }
            if (!$request->has('course_ids')) {
                throw new BusinessLogicException('course_ids are required for certificate award');
            }

            $file = $this->saveAwardFile($request);
            $success = Award::query()->create([
                'award_category_id' => $request->input('award_category_id'),
                'path' => $file['relative'],
                'format' => $file['format'],
            ]);

            if ($success) {
                Course::whereIn('id', $request->input('course_ids'))
                    ->update(['award_id' => $success->id]);

            }
            return $success;
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

            $file = $this->saveAwardFile($request);
            $parameters['format'] = $file['format'];
            $parameters['path'] = $file['relative'];

            $this->updateCourses($request->input('course_ids', []), $award);

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
            return [];
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

        foreach ($myAwards as $item) {
            if (!isset($result[$item->id])) {
                $result[$item->id] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'type' => $item['type']
                ];
            }
            $result[$item->id]['my'] = $item['awards']->map(function ($item) use ($user) {
                return [
                    'id' => $item->id,
                    'award_category_id' => $item->award_category_id,
                    'path' => FileHelper::getUrl($this->path, $item->users[0]->pivot->path),
                ];
            });
        }

        foreach ($availableAwards as $item) {
            if (!isset($result[$item->id])) {
                $result[$item->id] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'type' => $item['type']
                ];
            }
            $result[$item->id]['available'] = $item['awards'];

        }
        foreach ($otherAwards as $item) {
            if (!isset($result[$item->id])) {
                $result[$item->id] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'type' => $item['type']
                ];
            }

            $result[$item->id]['other'] = $item['awards'];

        }


        return array_values($result);
    }


}