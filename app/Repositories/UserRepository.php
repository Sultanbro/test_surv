<?php

namespace App\Repositories;

use App\User as Model;
use Illuminate\Support\Facades\DB;

/**
 * Класс для работы с Repository.
 */
class UserRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @return array
     */
    public function getIdFullName(): array
    {
        return $this->model()->withTrashed()->select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"), 'ID as id')->get()->toArray();
    }

    /**
     * @param array $userIds
     * @param int $year
     * @param int $month
     * @return object
     */
    public function userTimeTrackRelation(
        array $userIds,
        int $year,
        int $month
    ): object
    {
        return $this->model()->withTrashed()->selectRaw("*,CONCAT(name,' ',last_name) as full_name")
            ->with([
                'timetracking' => fn ($time) => $time->selectRaw("*, DATE_FORMAT(`enter`, '%e') as date")
                        ->orderBy('date')
                        ->whereMonth('enter', $month)
                        ->whereYear('enter', $year)
        ])->whereIn('id', $userIds)->get();
    }
}