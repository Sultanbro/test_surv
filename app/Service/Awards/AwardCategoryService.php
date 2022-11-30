<?php

namespace App\Service\Awards;

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
    public function storeAwardCategory($request): mixed
    {
        try {
            $type = AwardCategory::query()->create($request->all());
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
    public function updateAwardCategory($request, AwardCategory $awardCategory): bool
    {
        try {
            return $awardCategory->update($request->all());
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

}