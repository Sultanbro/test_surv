<?php

namespace App\Service\Award;

use App\Http\Requests\StoreAwardRequest;
use App\Models\Award;
use App\Models\AwardType;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AwardService
{
    /**
     * Сохраняем тип награды.
     * @param $request
     * @return mixed
     */
    public function storeAwardType($request): mixed
    {
        try {
            $type = AwardType::query()->create($request->all());

            return response()->success($type);
        }catch (\Exception $exception) {
            return response()->error($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $request
     * @param AwardType $awardType
     * @return bool
     * @throws Exception
     */
    public function updateAwardType($request, AwardType $awardType): bool
    {
        try {
            return $awardType->update($request->all());
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param StoreAwardRequest $request
     * @return mixed
     * @throws Exception
     */
    public function storeAward(StoreAwardRequest $request)
    {
        try {
            $success = Award::query()->create([
                'award_type_id' => $request->input('award_type_id'),
                'format'    => $request->file('image')->extension(),
                'path'      => $this->saveAwardFile($request)
            ]);

            return response()->success($success);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function updateAward($request, Award $award): bool
    {
        try {
            return $award->update($request->all());
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $request
     * @return string
     */
    private function saveAwardFile($request): string
    {
        $extension  = $request->file('image')->extension();
        $path       = public_path('awards/');
        $awardFileName = time() . '.' . $extension;
        $request->file('image')->move($path, $awardFileName);

        return $awardFileName;
    }
}