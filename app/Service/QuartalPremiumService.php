<?php

namespace App\Service;

use App\Events\TrackQuartalPremiumEvent;
use App\Http\Requests\QuartalPremiumSaveRequest;
use App\Http\Requests\QuartalPremiumUpdateRequest;
use App\Models\QuartalPremium;
use App\Traits\KpiHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class QuartalPremiumService
{
    use KpiHelperTrait;
    /**
     * @param Request $request
     * @return array
     */
    public function get(Request $request):array
    {
        try {
            $id = $request->input('id');

            return QuartalPremium::query()->find($id)->toArray();

        }catch (\DomainException $exception){
            throw new \DomainException($exception);
        }
    }

    /**
     * @param QuartalPremiumSaveRequest $request
     * @return array
     */
    public function save(QuartalPremiumSaveRequest $request): array
    {
        try {
            $model = $this->getModel($request->input('targetable_type'));

            QuartalPremium::query()->create([
                'targetable_id'     => $request->input('targetable_id'),
                'targetable_type'   => $model,
                'activity_id'       => $request->input('activity_id'),
                'title'             => $request->input('title'),
                'text'              => $request->input('text'),
                'plan'              => $request->input('plan'),
                'from'              => $request->input('from'),
                'to'                => $request->input('to')
            ]);

            return [
                'status' => ResponseAlias::HTTP_OK,
                'message' => 'success'
            ];

        }catch (\DomainException $exception){
            throw new \DomainException($exception);
        }
    }

    /**
     * @param QuartalPremiumUpdateRequest $request
     * @return array
     */
    public function update(QuartalPremiumUpdateRequest $request): array
    {
        try {
            $quartalPremiumId = $request->input('quartal_premium_id');

            event(new TrackQuartalPremiumEvent($quartalPremiumId));

            QuartalPremium::query()->findOrFail($quartalPremiumId)->update($request->all());

            return [
                'status' => ResponseAlias::HTTP_OK,
                'message' => 'success'
            ];
        }catch (\DomainException $exception){
            throw new \DomainException($exception);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function delete(Request $request): array
    {
        try {
            $request->only(['id']);

            $id = $request->input('id');

            QuartalPremium::query()->find($id)->delete();

            return [
                'status' => ResponseAlias::HTTP_OK,
                'message' => 'success'
            ];
        }catch (\DomainException $exception){

        }
    }
}