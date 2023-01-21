<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Classes\Helpers\Phone;
use App\Enums\ErrorCode;
use App\Enums\UserFilterEnum;
use App\Events\EmailNotificationEvent;
use App\Support\Core\CustomException;
use App\User as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Matrix\Builder;

/**
 * Класс для работы с Repository.
 */
final class UserRepository extends CoreRepository
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

    public function getUserByEmail(
        string $email
    )
    {
        return $this->model()->where('email', $email)->first() ?? null;
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
            ->where(function($query){
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
            ->when($endDateDeactivate, fn($q) => $q->whereDate('users.deleted_at', '<=', $endDateDeactivate));
    }

    /**
     * @param array $userData
     * @return void
     */
    public function updateOrCreateNewEmployee(
        array $userData
    ): void
    {
        $password = str_random(8);
        $this->model()->updateOrCreate(
            [
                'email' => $userData['email']
            ],
            [
                'email'             => strtolower($userData['email']),
                'name'              => $userData['name'],
                'last_name'         => $userData['last_name'],
                'description'       => $userData['description'],
                'password'          => Hash::make($password),
                'position_id'       => $userData['position_id'],
                'user_type'         => $userData['user_type'],
                'timezone'          => 6,
                'birthday'          => $userData['birthday'],
                'program_id'        => $userData['program_type'],
                'working_day_id'    => $userData['working_days'],
                'working_time_id'   => $userData['working_times'],
                'phone'             => Phone::normalize($userData['phone']),
                'full_time'         => $userData['full_time'],
                'work_start'        => $userData['work_start_time'],
                'work_end'          => $userData['work_end_time'],
                'currency'          => $userData['currency'] ?? 'kzt',
                'weekdays'          => $userData['weekdays'],
                'working_country'   => $userData['working_country'],
                'working_city'      => $userData['working_city'],
                'role_id'           => $userData['role_id'] ?? 1,
                'is_admin'          => $userData['is_admin'] ?? 0,
                'img_url'           => $userData['file_name']
            ]
        );
        EmailNotificationEvent::dispatch($userData['name'], $userData['email'], $password);
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
        int $userId,
        ?string $salary = '70000',
        ?string $cardNumber,
        ?string $kaspi,
        ?string $jysan,
        ?string $cardKaspi,
        ?string $cardJysan,
        ?string $kaspiCardholder,
        ?string $jysanCardholder
    )
    {
        $this->model()->find($userId)->zarplata()->updateOrCreate(
            [
                'user_id' => $userId
            ],
            [
                'zarplata' => $salary,
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
        int $userId,
        array $relations = []
    ): object
    {
        return $this->model()->withTrashed()
            ->when(isset($relations), fn ($user) => $user->with($relations))
            ->findOrFail($userId);
    }

    /**
     * @param int $userId
     * @param array $permissions
     * @return bool
     */
    public function switchPermission(
        int $userId,
        array $permissions,
    ): bool
    {
        foreach ($permissions as $permission)
        {
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
}