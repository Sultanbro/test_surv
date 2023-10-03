<?php

namespace App\Service\Award\AwardType;

use App\Enums\AwardTypeEnum;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;

class NominationAwardService implements AwardInterface
{
    public $path = 'awards';


    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function fetch(array $params): array
    {
        $result = [];
        $user = User::query()->findOrFail($params['user_id']);

        try {
            $type = AwardTypeEnum::TYPES[$params['key']];

            $result['my'] =Award::whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->whereHas('category', function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->with(['users' => function ($query) use($user) {
                    $query->select('users.id', 'users.name', 'users.last_name', 'users.avatar')
                        ->where('users.id', $user->id);
                }, 'category'])
                ->get();

            $result['available'] = AwardCategory::query()
                ->where('type', $type)
                ->with('awards', function ($query) use ($user) {
                    $query->whereDoesntHave('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });

                })
                ->get();


            $result['other'] = Award::whereHas('users', function ($query) use ($user) {
                $query->whereNot('users.id', $user->id);
            })
                ->whereHas('category', function ($query) use ($type) {
                    $query->where('hide', false)
                        ->where('type', $type);
                })
                ->with(['users' => function ($query) use($user) {
                    $query->select('users.id', 'users.name', 'users.last_name', 'users.avatar')
                    ->whereNot('users.id', $user->id);
                }, 'category'])
                ->get();

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
        return $this->mapAwardsData($result);
    }

    /**
     * @param StoreAwardRequest $request
     * @return mixed
     * @throws Exception
     */
    public function store(StoreAwardRequest $request)
    {
        $awards = [];
        try {
            $files = $this->saveAwardFiles($request);
            $previews = $this->saveAwardPreviews($request);

            if (count($files) != count($previews)) {
                throw new Exception("Files and previews count not equal!");
            }

            for ($i = 0; $i < count($files); $i++) {
                $awards[] = Award::query()->create([
                    'award_category_id' => $request->input('award_category_id'),
                    'path' => $files[$i]['relative'],
                    'format' => $files[$i]['format'],
                    'preview_path' => $previews[$i]['relative'],
                    'preview_format' => $previews[$i]['format'],
                    'type' => $request->input('type') ?? Award::TYPE_PUBLIC
                ]);
            }

            return \response()->success($awards);
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
    public function update(UpdateAwardRequest $request, Award $award): bool
    {
        try {
            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * @param $request
     * @return array
     * @throws BusinessLogicException
     */
    private function saveAwardFiles($request): array
    {
        if (!$request->hasFile('file')) {
            return [];
        }
        $files = [];

        foreach ($request->file('file') as $file) {
            if (!$filename = FileHelper::save($file, $this->path)) {
                throw new BusinessLogicException(__('exception.save_error'));
            }
            $files[] = [
                'relative' => $filename,
                'format' => $file->getClientOriginalExtension(),
                'temp' => FileHelper::getUrl($this->path, $filename)
            ];
        }

        return $files;
    }

    /**
     * @param $request
     * @return array
     * @throws BusinessLogicException
     */
    private function saveAwardPreviews($request): array
    {
        if (!$request->hasFile('preview')) {
            return [];
        }
        $previews = [];

        foreach ($request->file('preview') as $preview) {
            if (!$filename = FileHelper::save($preview, $this->path)) {
                throw new BusinessLogicException(__('exception.save_error'));
            }
            $previews[] = [
                'relative' => $filename,
                'format' => $preview->getClientOriginalExtension(),
                'temp' => FileHelper::getUrl($this->path, $filename)
            ];
        }

        return $previews;
    }

    private function mapAwardsData(array $data): array
    {
        $result = [];

        $myAwards = $data['my'];
        $availableAwards = $data['available'];
        $otherAwards = $data['other'];

        $myAwardsRead = $myAwards->isEmpty() || !$myAwards->contains(fn($a) => !$a->read);

        foreach ($myAwards as $item) {
            if (!isset($result[$item->category->id])) {
                $result[$item->category->id] = [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'description' => $item->category->description,
                    'type' => $item->category->type,
                ];
            }
            $user = $item->users[0];
            $result[$item->category->id]['my'][] = [
                'award_id' => $item->id,
                'award_category_id' => $item->category->id,
                'format' =>$user->pivot->format,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'user_id' => $user->id,
                'tempPath' => FileHelper::getUrl($this->path, $user->pivot->path),
            ];
        }

        foreach ($availableAwards as $item) {
            if (!isset($result[$item->id])) {
                $result[$item->id] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'type' => $item['type'],
                ];
            }
            $result[$item->id]['available'] = $item['awards'];

        }

        foreach ($otherAwards as $item) {
            if (!isset($result[$item->category->id])) {
                $result[$item->category->id] = [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'description' => $item->category->description,
                    'type' => $item->category->type,
                ];
            }
            foreach ($item->users as $user) {
                $result[$item->category->id]['other'][] = [
                    'award_id' => $item->id,
                    'award_category_id' => $item->category->id,
                    'format' => $user->pivot->format,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'user_id' => $user->id,
                    'tempPath' => FileHelper::getUrl($this->path, $user->pivot->path),
                ];
            }
        }

        return ['data' => array_values($result), 'read' => $myAwardsRead];
    }

}
