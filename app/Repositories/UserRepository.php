<?php

namespace App\Repositories;

use App\Enums\UserFilterEnum;
use App\User as Model;
use Illuminate\Support\Facades\DB;
use Matrix\Builder;

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

    /**
     * @param string $type
     * @param int $positionId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $startDateDeactivate
     * @param string|null $endDateDeactivate
     * @param string|null $startDateApplied
     * @param string|null $endDateApplied
     * @param int|null $segment
     * @param bool $isTrainee
     * @return object
     */
    public function userWithDescription(
        string $type,
        int $positionId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $startDateDeactivate = null,
        ?string $endDateDeactivate = null,
        ?string $startDateApplied = null,
        ?string $endDateApplied = null,
        ?int $segment = 0,
        bool $isTrainee = false
    ): object
    {
        return $this->model()->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', $isTrainee)
            ->when($type == UserFilterEnum::DEACTIVATED, fn($q) => $q->whereNotNull('deleted_at'))
            ->when($positionId != 0, fn ($q) => $q->where('position_id', $positionId))
            ->when($startDate, fn($q) => $q->whereDate('users.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('users.created_at', '<=', $endDate))
            ->when($startDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '>=', $startDateDeactivate))
            ->when($endDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '<=', $endDateDeactivate))
            ->when($startDateApplied, fn($q) => $q->whereDate('users.applied', '>=', $startDateApplied))
            ->when($endDateApplied, fn($q) => $q->whereDate('users.applied', '<=', $endDateApplied))
            ->when($segment != 0, fn($q) => $q->where('users.segment', $segment))->get();
    }

    /**
     * @return object
     */
    public function userWithDownloads(): object
    {
        return $this->model()
            ->join('profile_downloads as pd', 'pd.user_id', '=', 'users.id')
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->where(function($query){
                $query->whereNull('users.position_id')
                    ->orWhereNull('users.phone')
                    ->orWhereNull('users.birthday')
                    ->orWhereNull('users.working_day_id')
                    ->orWhereNull('users.working_time_id');
            }
        )->get();
    }

    /**
     * @param int $positionId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $startDateDeactivate
     * @param string|null $endDateDeactivate
     * @return object
     */
    public function getTrainees(
        int $positionId,
        ?string $startDate,
        ?string $endDate,
        ?string $startDateDeactivate,
        ?string $endDateDeactivate
    ): object
    {
        return $this->model()
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->when($positionId != 0, fn ($q) => $q->where('position_id', $positionId))
            ->where('is_trainee', 1)
            ->whereNull('users.deleted_at')
            ->whereNull('ud.fire_date')
            ->when($startDate, fn($q) => $q->whereDate('users.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('users.created_at', '<=', $endDate))
            ->when($startDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '>=', $startDateDeactivate))
            ->when($endDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '<=', $endDateDeactivate))->get();
     }
}