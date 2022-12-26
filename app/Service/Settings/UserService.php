<?php

namespace App\Service\Settings;

use App\Exports\UserExport;
use App\Filters\Users\UserFilter;
use App\Filters\Users\UserFilterBuilder;
use App\Repositories\ProfileGroupRepository;
use App\User;
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
        public UserFilterBuilder $builder,
        public UserFilter $filter
    )
    {}

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
                $user->groups = isset($user->deleted_at) ? $user->firedGroups() :$user->inGroups()->pluck('id')->toArray();

                $user->deleted_at = isset($user->deleted_at) ? Carbon::parse($user->deleted_at)->addHours(6)->format('Y-m-d H:i:s') : null;
                $user->created_at = isset($user->created_at) ? Carbon::parse($user->created_at)->addHours(6)->format('Y-m-d H:i:s') : null;
                $user->applied    = isset($user->applied) ? Carbon::parse($user->applied)->addHours(6)->format('Y-m-d H:i:s') : null;

                return $user;
            });

            if ($filters['excel'])
            {
                $this->export($users, $groups);
            }

            return [
                'can_login_users' => [5, 18],
                'users'         => $users,
                'groups'        => $groups,
                'auth_token'    => Auth::user()->remember_token ?? User::query()->find(5),
                'currentUser'   => Auth::user()->id ?? 5,
                'start_date'    => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date'      => Carbon::now()->endOfMonth()->format('Y-m-d'),
            ];

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $users
     * @param $groups
     * @return BinaryFileResponse
     */
    private function export($users, $groups): BinaryFileResponse
    {
        $export = new UserExport($users, $groups);
        $title = 'Сотрудники: ' . date('Y-m-d') . '.xlsx';
        return Excel::download($export, $title);
    }
}