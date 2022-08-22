<?php

namespace App\Service;

use App\Events\TrackQuartalPremiumEvent;
use App\Http\Requests\QuartalPremiumSaveRequest;
use App\Http\Requests\QuartalPremiumUpdateRequest;
use App\Models\QuartalPremium;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Models\Analytics\Activity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ProfileGroup;
use App\Traits\KpiHelperTrait;

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

            return QuartalPremium::find($request->id)->toArray();

        } catch (\DomainException $exception){
            throw new \DomainException($exception);
        }
    }

    /**
     * вытащить все квартальные премии
     */
    public function fetch($filters): array
    {   
        if($filters !== null) {} 
        
        return [
            'items'      => QuartalPremium::with('creator', 'updater')->get(),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * @param QuartalPremiumSaveRequest $request
     * @return array
     */
    public function save(QuartalPremiumSaveRequest $request): array
    {
        try {

            $quartal_premium = QuartalPremium::query()->create([
                'targetable_id'     => $request->input('targetable_id'),
                'targetable_type'   => $request->targetable_type,
                'activity_id'       => $request->input('activity_id'),
                'title'             => $request->input('title'),
                'text'              => $request->input('text'),
                'plan'              => $request->input('plan'),
                'from'              => $request->input('from'),
                'to'                => $request->input('to')
            ]);

            return [
                'quartal_premium' => $quartal_premium
            ];

        } catch (\DomainException $exception){
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

            $all = $request->all();
            $all['updated_by'] = auth()->id();

            $id = $request->id;
            event(new TrackQuartalPremiumEvent($id));

            QuartalPremium::findOrFail($id)->update($all);

            return [
                'status'  => ResponseAlias::HTTP_OK,
                'message' => 'success'
            ];
        } catch (\DomainException $exception){
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
            QuartalPremium::find($request->id)->delete();

            return [
                'status'  => ResponseAlias::HTTP_OK,
                'message' => 'success'
            ];
        } catch (\DomainException $exception){

        }
    }
}