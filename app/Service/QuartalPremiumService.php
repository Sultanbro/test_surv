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
        
        $items = QuartalPremium::with('creator', 'updater')->get();

        return [
            'items'      =>  $this->groupItems($items), 
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Группировать премии
     * Копия метода с бонусов
     */
    private function groupItems($items) {
        $arr = [];

        $types = $items->where('target', '!=', null)->groupBy('target.type');
 
        foreach ($types as $type => $type_items) {
            foreach ($type_items->groupBy('target.name') as $name => $name_items) {
                $arr[] = [
                    'type'     => $type,
                    'name'     => $name,
                    'id'       => $name_items[0]->target['id'],
                    'items'    => $name_items,
                    'expanded' => false
                ];
            }
        }
        
        return $arr;
    }

    /**
     * @param QuartalPremiumSaveRequest $request
     * @return array
     */
    public function save(QuartalPremiumSaveRequest $request): array
    {
        try {

            $quartal_premium = QuartalPremium::query()->create([
                'targetable_id'     => $request->targetable_id,
                'targetable_type'   => $request->targetable_type,
                'activity_id'       => $request->input('activity_id'),
                'title'             => $request->input('title'),
                'text'              => $request->input('text'),
                'plan'              => $request->input('plan'),
                'from'              => $request->input('from'),
                'to'                => $request->input('to'),
                'sum'               => $request->input('sum'),
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
    public function delete(Request $request): void
    {
        QuartalPremium::findOrFail($request->id)->delete();
    }
}