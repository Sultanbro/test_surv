<?php

namespace App\Service\Awards\AwardTypes;

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

            $result['my'] =  AwardCategory::query()
                ->where('type', $type)
                ->with('awards',function ($query) use ($user){
                    $query->whereHas('users',function ($q) use ($user){
                        $q->where('users.id', $user->id);
                    });
                })
                ->get();

            $result['available']  =  AwardCategory::query()
                ->where('type', $type)
                ->with('awards',function ($query) use ($user){
                    $query->whereDoesntHave('users',function ($q) use ($user){
                        $q->where('users.id', $user->id);
                    });
                })
                ->get();

            $result['other'] = AwardCategory::query()
                ->where('type', $type)
                ->with('awards',function ($query) use ($user){
                    $query->with('users', function ($q){
                        $q->select('users.id', 'users.name', 'users.last_name', 'users.avatar');
                    })
                        ->whereHas('users',function ($q) use ($user){
                            $q->whereNot('users.id', $user->id);
                        });
                })
                ->where('hide', false)
                ->get();
        }catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
        return $this->mapAwardsData($result);
    }

    /**
     * @param StoreAwardRequest $request
     * @return array
     * @throws Exception
     */
    public function store(StoreAwardRequest $request): array
    {
        $awards = [];
        try {
            $files = $this->saveAwardFiles($request);
            foreach ($files as $file) {
                $awards[] = Award::query()->create([
                    'award_category_id' => $request->input('award_category_id'),
                    'path'      => $file['relative'],
                    'format'      => $file['format'],
                ]);

            }
            return $awards;
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
        if (!$request->hasFile('file')){
            return [];
        }
        $files = [];

        foreach($request->file('file') as $file)
        {
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

    private function mapAwardsData(array $data): array{
        $result = [];

        $myAwards = $data['my'];
        $availableAwards = $data['available'];
        $otherAwards = $data['other'];

        foreach ($myAwards as $item){
            if (!isset($result[$item->id])){
                $result[$item->id]['id'] = $item['id'];
                $result[$item->id]['name'] = $item['name'];
                $result[$item->id]['description'] = $item['description'];
                $result[$item->id]['type'] = $item['type'];
            }
            $result[$item->id]['my'] = $item['awards']->map(function ($item) {
                return [
                    'id' => $item->id,
                    'award_category_id' => $item->award_category_id,
                    'format' => $item->format,
                    'path' => FileHelper::getUrl($this->path, $item->users[0]->pivot->path),
                ];
            });

        }
        foreach ($availableAwards as $item){
            if (!isset($result[$item->id])){
                $result[$item->id]['id'] = $item['id'];
                $result[$item->id]['name'] = $item['name'];
                $result[$item->id]['description'] = $item['description'];
                $result[$item->id]['type'] = $item['type'];
            }
            $result[$item->id]['available'] = $item['awards'];

        }
        foreach ($otherAwards as $item){
            if (!isset($result[$item->id])){
                $result[$item->id]['id'] = $item['id'];
                $result[$item->id]['name'] = $item['name'];
                $result[$item->id]['description'] = $item['description'];
                $result[$item->id]['type'] = $item['type'];
            }

            $result[$item->id]['other'] = $item['awards'];

        }



        return array_values($result);
    }

}