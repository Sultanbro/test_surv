<?php

namespace App\Service\Settings;

use App\AdaptationTalk;
use App\DTO\Settings\StoreUserDTO;
use App\Enums\ErrorCode;
use App\Events\TimeTrack\CreateTimeTrackHistoryEvent;
use App\Exports\UserExport;
use App\Filters\Users\UserFilter;
use App\Filters\Users\UserFilterBuilder;
use App\Helpers\FileHelper;
use App\Helpers\UserHelper;
use App\Models\Bitrix\Segment;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Position;
use App\Repositories\DayTypeRepository;
use App\Repositories\ProfileGroupRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\UserDescriptionRepository;
use App\Repositories\UserRepository;
use App\Service\Department\UserService as DepartmentUserService;
use App\Service\Tenancy\CabinetService;
use App\Setting;
use App\Support\Core\CustomException;
use App\User;
use App\WorkingDay;
use App\WorkingTime;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Класс для работы с Service.
 */
class UserService
{
    public function __construct(
        public UserFilterBuilder         $builder,
        public UserFilter                $filter,
        public UserRepository            $userRepository,
        public UserDescriptionRepository $descriptionRepository
    )
    {
    }

    /**
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function get(
        array $filters
    ): array
    {
        try {
            $this->builder->setBuilder($this->filter);
            $groups = (new ProfileGroupRepository)->getGroupsIdNameWithPluck(true);
            $users = $this->builder->getFilter($filters)->map(function ($user) {
                $user->groups = isset($user->deleted_at) ? $user->firedGroups() : $user->inGroups()->pluck('id')->toArray();

                $user->deleted_at = isset($user->deleted_at) ? Carbon::parse($user->deleted_at)->addHours(6)->format('Y-m-d H:i:s') : null;
                $user->created_at = isset($user->created_at) ? Carbon::parse($user->created_at)->addHours(6)->format('Y-m-d H:i:s') : null;
                $user->applied = isset($user->applied) ? Carbon::parse($user->applied)->addHours(6)->format('Y-m-d H:i:s') : null;

                return $user;
            });

            if ($filters['excel']) {
                $this->export($users, $groups);
            }

            return [
                'can_login_users' => [5, 18],
                'users' => $users,
                'groups' => $groups,
                'auth_token' => Auth::user()->remember_token ?? User::query()->find(5),
                'currentUser' => Auth::user()->id ?? 5,
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
            ];

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function userStore(
        StoreUserDTO $dto
    ): User
    {

        $user = $this->userRepository->getUserByEmail($dto->email);

        if ($user) {
            new CustomException('Пользователь с указанным email или данными уже существует в системе', ErrorCode::BAD_REQUEST, []);
        }

        $tariffPlan = TariffPayment::getValidTariffPayments();

        $userLimit = Tariff::$defaultUserLimit;

        if ($tariffPlan) {
            $userLimit = $tariffPlan->total_user_limit;
        }

        $usersCount = User::query()->count();

        if ($usersCount >= $userLimit) {
            new CustomException('users limited by tariff', 401, [
                'usersLimited' => true,
            ]);
        }

        if ($user != null && $user->deleted_at != null) {
            $this->userRepository->restoreUser($user);
        }

        $user = $this->userRepository->updateOrCreateNewEmployee($dto);

        (new CabinetService)->add(tenant('id'), $user, false);

        (new DepartmentUserService)->setGroup($dto->group, $user->id, 'add');

        if ($dto->headGroup != 0 && $dto->positionId == Position::GROUP_HEAD) {
            $this->setProfileGroupHead($user->id, $dto->headGroup);
        }

        $this->setUserDescription($user->id, (bool)$dto->isTrainee);

        if ($dto->contacts) {
            UserHelper::saveContacts($user->id, $dto->contacts['phone']);
        }

        if ($dto->cards) {
            UserHelper::saveCards($user->id, $dto->cards);
        }

        if (
            $dto->file1 || $dto->file2 ||
            $dto->file3 || $dto->file4 ||
            $dto->file5 || $dto->file6 ||
            $dto->file7
        ) {
            FileHelper::storeDocumentsFile([
                'dog_okaz_usl' => $dto->file1,
                'sohr_kom_tainy' => $dto->file2,
                'dog_o_nekonk' => $dto->file3,
                'trud_dog' => $dto->file4,
                'ud_lich' => $dto->file5,
                'photo' => $dto->file6,
                'archive' => $dto->file7
            ], $user->id);
        }

        $this->userRepository->updateOrCreateSalary(
            $user->id,
            $dto->cardNumber,
            $dto->kaspi,
            $dto->jysan,
            $dto->cardKaspi,
            $dto->cardJysan,
            $dto->kaspiCardholder,
            $dto->jysanCardholder,
            $dto->salary,
        );

        return $user;

    }

    /**
     * @param int|null $id
     * @return array
     */
    public function userSetting(
        ?int $id
    ): array
    {
        return [
            'positions' => Position::all(),
            'user' => isset($id) ? $this->userData($id)['user'] : null,
            'fire_causes' => isset($id) ? $this->userData($id)['fire_causes'] : null,
            'corpBooks' => isset($id) ? $this->userRepository->userWithKnowBaseModel($id)->get([
                'kb.*'
            ]) : [],
            'groups' => (new ProfileGroupRepository)->getActive(),
            'programs' => (new ProgramRepository)->getProgramByDesc(),
            'workingDays' => WorkingDay::all(),
            'workingTimes' => WorkingTime::all(),
            'timezones' => Setting::TIMEZONES
        ];
    }

    /**
     * @param int $userId
     * @return array
     */
    private function userData(
        int $userId
    ): array
    {
        $user = $this->userRepository->userWithRelations($userId, [
            'zarplata',
            'downloads',
            'user_description',
            'cards',
            'lead',
            'groups'
        ]);

        $user->segment = Segment::query()->find(0)->name ?? '';

        UserHelper::workWithUs($user);
        $fireCauses = UserHelper::fireCause($user->user_description->is_trainee);
        $user->applied_at = $user->user_description->is_trainee ? $user->applied : $user->created_at;
        $user->head_in_groups = $user->inGroups(true)->toArray();

        if ($user->user_description) {
            $user->in_books = '[]';
        }

        $user->adaptation_talks = AdaptationTalk::getTalks($user->id);

        return [
            'fire_causes' => $fireCauses,
            'user' => $user
        ];
    }


    /**
     * @throws Exception
     */
    private function setUserDescription(
        int   $userId,
        ?bool $isTrainee = false
    ): void
    {
        if ($isTrainee) {
//            $this->descriptionRepository->setEmployee($userId); // TODO: why this in is_trainee
            (new DayTypeRepository)->createNew($userId);
        }

        $this->descriptionRepository->touchDescription($userId, $isTrainee);

        CreateTimeTrackHistoryEvent::dispatch($userId);
    }

    /**
     * @param int $userId
     * @param int $groupId
     * @return void
     */
    private function setProfileGroupHead(
        int $userId,
        int $groupId
    ): void
    {
        (new ProfileGroupRepository)->setHead($userId, $groupId);
    }

    /**
     * @param $users
     * @param $groups
     * @return BinaryFileResponsed
     */
    private function export($users, $groups): BinaryFileResponse
    {
        $export = new UserExport($users, $groups);
        $title = 'Сотрудники: ' . date('Y-m-d') . '.xlsx';
        return Excel::download($export, $title);
    }
}