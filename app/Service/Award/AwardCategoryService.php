<?php

namespace App\Service\Award;

use App\Models\Award\AwardCategory;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AwardCategoryService
{

    /**
     * Сохраняем тип награды.
     * @param $request
     * @return mixed
     */
    public function storeAwardCategory($params): mixed
    {
        try {
            $type = AwardCategory::query()->create($params);
            return response()->success($type);
        }catch (\Exception $exception) {
            return response()->error($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param $request
     * @param AwardCategory $awardCategory
     * @return bool
     * @throws Exception
     */
    public function updateAwardCategory($params, AwardCategory $awardCategory): bool
    {
        try {
            return $awardCategory->update($params);
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

}