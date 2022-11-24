<?php

namespace App\Service;

use App\Events\BonusUpdated;
use App\Http\Requests\BonusSaveRequest;
use App\Http\Requests\BonusUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\GroupUser;
use App\Models\Kpi\Bonus;
use App\Repositories\KpiBonusRepository;
use App\Traits\KpiHelperTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ProfileGroup;
use App\Exceptions\Kpi\TargetDuplicateException;

class BonusService
{
    use KpiHelperTrait;

    private KpiBonusRepository $repository;

    public function __construct(KpiBonusRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Получить бонусы пользователя
     * @param int $user_id
     * @return array
     */
    public function getUserBonuses(int $user_id): array
    {
        $groups =  GroupUser::where('user_id', $user_id)
            ->where('status', 'active')
            ->get()
            ->pluck('group_id')
            ->toArray();

        $bonuses = Bonus::whereIn('targetable_id', $groups)
            ->where('targetable_type', 'App\ProfileGroup')->get();
        return [
            'bonuses'    => $this->groupItems($bonuses),
        ];
    }

    /**
     * Получить бонус и активности и группы.
     * @param int $id
     */
    public function get(int $id): array
    {
        return [
            'bonuses'    => Bonus::query()->findOrFail($id),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Получить бонусы и активности и группы.
     * @param array $filters
     */
    public function fetch($filters): array
    {   
        if($filters !== null) {} 
        
        $bonuses = Bonus::with('creator', 'updater')->get();
 
        return [
            'bonuses'    => $this->groupItems($bonuses),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Группировать бонусы
     */
    private function groupItems($items) : array
    {
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
     * Сохраняем новый бонус.
     * @throws Exception
     */
    public function save(array $data): array
    {
        try {
            $data['created_by'] = auth()->id() ?? 5;
            $data['updated_by'] = auth()->id() ?? 5;
            $data['targetable_type'] = $this->getModel($data['targetable_type']);

            return [
                'bonus' => $this->repository->saveNewBonus($data)
            ];
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    /**
     * Обновляем данные и сохраняем в histories старые данные.
     */
    public function update(BonusUpdateRequest $request) : void
    {
        try {

            $id = $request->id;
            event(new BonusUpdated($id));
          
            $all = $request->all();
            $all['updated_by'] = auth()->id();

            Bonus::query()->findOrFail($id)->update($all);

        } catch (Exception $exception){
            Log::error($exception);
            
            throw new Exception($exception);
        }
    }

    /**
     * Удалить бонус
     */
    public function delete(int $id) : void
    {
        Bonus::query()->findOrFail($id)->delete();
    }

    private function getData(array $data)
    {
        foreach ($data as $item)
        {
            return $item;
        }
    }
}