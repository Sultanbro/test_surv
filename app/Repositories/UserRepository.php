<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Classes\Helpers\Phone;
use App\DTO\Settings\StoreUserDTO;
use App\Enums\UserFilterEnum;
use App\Events\EmailNotificationEvent;
use App\Models\Bitrix\Segment;
use App\Models\CentralUser;
use App\Models\UserCoordinate;
use App\User;
use App\User as Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Класс для работы с Repository.
 */
final class UserRepository extends CoreRepository
{
    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return  Collection<User>
     */
    public function betweenDate(Carbon $startDate, Carbon $endDate): Collection
    {
        return User::withTrashed()
            ->withWhereHas('user_description', fn($query) => $query->where('is_trainee', false))
            ->with(['salaries' => fn($query) => $query->whereBetween('date', [
                $startDate->format("Y-m-d"),
                $endDate->format("Y-m-d")
            ])])
            ->with('zarplata')
            ->where(fn($query) => $query
//                ->whereNull('deleted_at')
//                ->orWhere(fn($query) => $query->whereBetween('deleted_at', [
//                    $startDate->format("Y-m-d"),
//                    $endDate->format("Y-m-d")
//                ]))
            )
            ->get();
    }

    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    public function getUserByEmail(
        string $email
    ): ?Model
    {
        return $this->model()->where('email', strtolower($email))->first();
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
        int   $year,
        int   $month
    ): object
    {
        return $this->model()->withTrashed()->selectRaw("*,CONCAT(name,' ',last_name) as full_name")
            ->with([
                'timetracking' => fn($time) => $time->selectRaw("*, DATE_FORMAT(`enter`, '%e') as date")
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
        string  $type,
        int     $positionId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $startDateDeactivate = null,
        ?string $endDateDeactivate = null,
        ?string $startDateApplied = null,
        ?string $endDateApplied = null,
        ?int    $segment = 0,
        bool    $isTrainee = false
    ): object
    {
        return $this->model()->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', $isTrainee)
            ->when($type == UserFilterEnum::DEACTIVATED, fn($q) => $q->whereNotNull('deleted_at'))
            ->when($positionId != 0, fn($q) => $q->where('position_id', $positionId))
            ->when($startDate, fn($q) => $q->whereDate('users.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('users.created_at', '<=', $endDate))
            ->when($startDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '>=', $startDateDeactivate))
            ->when($endDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '<=', $endDateDeactivate))
            ->when($startDateApplied, fn($q) => $q->whereDate('users.applied', '>=', $startDateApplied))
            ->when($endDateApplied, fn($q) => $q->whereDate('users.applied', '<=', $endDateApplied))
            ->when($segment != 0, fn($q) => $q->where('users.segment', $segment));
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
            ->where(function ($query) {
                $query->whereNull('users.position_id')
                    ->orWhereNull('users.phone')
                    ->orWhereNull('users.birthday')
                    ->orWhereNull('users.working_day_id')
                    ->orWhereNull('users.working_time_id');
            }
            );
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
        int     $positionId,
        ?string $startDate,
        ?string $endDate,
        ?string $startDateDeactivate,
        ?string $endDateDeactivate
    ): object
    {
        return $this->model()
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->when($positionId != 0, fn($q) => $q->where('position_id', $positionId))
            ->where('is_trainee', 1)
            ->whereNull('users.deleted_at')
            ->whereNull('ud.fire_date')
            ->when($startDate, fn($q) => $q->whereDate('users.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('users.created_at', '<=', $endDate))
            ->when($startDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '>=', $startDateDeactivate))
            ->when($endDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '<=', $endDateDeactivate));
    }

    public function updateOrCreateNewEmployee(
        StoreUserDTO $dto
    ): User
    {
        $centralUser = CentralUser::where('email', $dto->email)->first();

        $segment = Segment::query()->where('name', 'Принят через Jobtron')->first();
        $password = str_random(8);

        $user = User::query()->updateOrCreate(
            [
                'email' => strtolower($dto->email)
            ],
            [
                'name' => $dto->name,
                'last_name' => $dto->lastName,
                'description' => $dto->description,
                'password' => isset($centralUser) ? $centralUser->password : Hash::make($password),
                'position_id' => $dto->positionId,
                'user_type' => $dto->userType,
                'timezone' => 6,
                'birthday' => $dto->birthday,
                'program_id' => $dto->programType,
                'working_day_id' => $dto->workingDays,
                'working_time_id' => $dto->workTimes,
                'phone' => Phone::normalize($dto->phone),
                'full_time' => $dto->fullTime,
                'currency' => $dto->currency ?? 'kzt',
                'weekdays' => $dto->weekdays,
                'working_country' => $dto->workingCountry,
                'working_city' => $dto->workingCity,
                'role_id' => $dto->role_id ?? 1,
                'is_admin' => $dto->is_admin ?? 0,
                'img_url' => $dto->fileName,
                'coordinate_id' => isset($dto->coordinates) ? $this->setCoordinate($dto->coordinates) : null,
                'segment' => $segment ? $segment->id : 57,
                'inviter_id' => \Auth::id()
            ]
        );

        $authData = null;

        if (!isset($centralUser)) {
            $authData = [
                'email' => $dto->email,
                'password' => $password,
            ];
        }

        EmailNotificationEvent::dispatch($dto->name, $dto->email, $authData);

        return $user;

    }

    /**
     * @param Model $user
     * @return void
     */
    public function restoreUser(
        Model $user
    ): void
    {
        $user->update([
            'deleted_at' => null
        ]);
    }

    public function updateOrCreateSalary(
        int     $userId,
        ?string $cardNumber,
        ?string $kaspi,
        ?string $jysan,
        ?string $cardKaspi,
        ?string $cardJysan,
        ?string $kaspiCardholder,
        ?string $jysanCardholder,
        ?string $salary,
    )
    {
        $this->model()->find($userId)->zarplata()->updateOrCreate(
            [
                'user_id' => $userId
            ],
            [
                'zarplata' => $salary ?? 70000,
                'card_number' => $cardNumber,
                'kaspi' => $kaspi,
                'jysan' => $jysan,
                'card_kaspi' => $cardKaspi,
                'card_jysan' => $cardJysan,
                'kaspi_cardholder' => $kaspiCardholder,
                'jysan_cardholder' => $jysanCardholder,
            ]
        );
    }

    /**
     * @param int $id
     * @return object
     */
    public function userWithKnowBaseModel(
        int $id
    ): object
    {
        return $this->model()->join('knowbase_model as kbm', function ($q) {
            $q->on('kbm.model_id', '=', 'users.id');
            $q->where('kbm.model_type', '=', 'App\User');
        })
            ->join('kb', 'kb.id', '=', 'kbm.book_id')
            ->where('kbm.model_id', $id);
    }

    /**
     * @param int $userId
     * @param array $relations
     * @return object
     */
    public function userWithRelations(
        int   $userId,
        array $relations = []
    ): object
    {
        return $this->model()->withTrashed()
            ->when(isset($relations), fn($user) => $user->with($relations))
            ->findOrFail($userId);
    }

    /**
     * @param int $userId
     * @param array $permissions
     * @return bool
     */
    public function switchPermission(
        int   $userId,
        array $permissions,
    ): bool
    {
        foreach ($permissions as $permission) {
            $user = $this->model()->find($userId);

            if ($user->permissions->contains($permission['id'])) {
                $user->permissions()->where('permission_id', $permission['id'])->update([
                    'is_access' => $permission['is_access']
                ]);
            } else {
                $user->permissions()->attach($permission['id'], [
                    'is_access' => $permission['is_access']
                ]);
            }
        }

        return true;
    }

    /**
     * @param string|null $date
     * @param $isTrainee bool
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getUsersWithDescription(
        ?string $date,
        bool    $isTrainee = false
    ): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model()
            ->withWhereHas('user_description', fn($query) => $query->where('is_trainee', $isTrainee))
            ->where(fn($query) => $query->whereNull('deleted_at')->orWhere(fn($query) => $query->whereDate('deleted_at', '>=', $date)));
    }

    public function setCoordinate(array $coordinatesArray)
    {
        $coordinate = UserCoordinate::query()
            ->where('geo_lat', $coordinatesArray['geo_lat'])
            ->where('geo_lon', $coordinatesArray['geo_lon'])
            ->first();

        if ($coordinate) {
            return $coordinate->id;
        } else {
            $coordinate = UserCoordinate::query()->create([
                'geo_lat' => $coordinatesArray['geo_lat'],
                'geo_lon' => $coordinatesArray['geo_lon']
            ]);
            return $coordinate->id;
        }
    }
}
