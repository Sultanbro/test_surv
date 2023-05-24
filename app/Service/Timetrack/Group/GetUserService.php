<?php
declare(strict_types=1);

namespace App\Service\Timetrack\Group;

use App\Repositories\KnowBase\KnowBaseRepository;
use App\Repositories\ProfileGroupRepository;
use App\Service\Department\UserService;
use Exception;

/**
* Класс для работы с Service.
*/
final class GetUserService
{
    public function __construct(
        public ProfileGroupRepository $profileGroupRepository,
        public KnowBaseRepository $knowBaseRepository
    )
    {}

    /**
     * @throws Exception
     */
    public function handle(
        ?string $type,
        ?int    $id
    )
    {
        $method = $type . 'GetData';
        if (!method_exists($this, $method)) {
            throw new Exception("Not method with name $method please create");
        }

        if ($id)
        {
            return $this->{$method}($id);
        }

        return $this->{$method}();
    }

    /**
     * @param int $id
     * @return array
     */
    protected function groupGetData(
        int $id
    ): array
    {
        $group = $this->profileGroupRepository->getGroup($id);

        $users = collect((new UserService)->getActualUsers($id, date('Y-m-d')))->map(function ($user) {
            $user->email = "$user->last_name $user->name $user->email";
            return $user;
        });

        $books = $this->profileGroupRepository->knowBasesBook($id);
        $corpBooks = $this->knowBaseRepository->getCorpBooks($books);

        return [
            'name'      => $group->name,
            'users'     => $users,
            'corp_books' => $corpBooks,
            'group_id'  => $group->id,
            'timeon'    => $group->work_start,
            'timeoff'   => $group->work_end,
            'work_chart_id' => $group->work_chart_id,
            'plan'      => $group->plan,
            'zoom_link' => $group->zoom_link,
            'bp_link'   => $group->bp_link,
            'dialer_id' => $group->dialer->dialer_id ?? null,
            'script_id' => $group->dialer->script_id ?? null,
            'talk_minutes'      => $group->dialer->talk_minutes ?? null,
            'talk_hours'        => $group->dialer->talk_hours ?? null,
            'quality'           => $group->quality,
            'bonuses'           => $this->profileGroupRepository->bonuses($id),
            'activities'        => $this->profileGroupRepository->activities($id),
            'payment_terms'     => $this->profileGroupRepository->paymentTerms($id),
            'time_exceptions'   => $group->time_exceptions,
            'time_address'      => $group->time_address,
            'workdays'          => $group->workdays,
            'editable_time'     => $group->editable_time,
            'paid_internship'   => $group->paid_internship,
            'show_payment_terms' => $group->show_payment_terms,
            'groups'            => $this->profileGroupRepository->getGroupsIdNameWithPluck(true),
            'archived_groups'   => $this->profileGroupRepository->getGroupsIdNameWithPluck(),
        ];
    }

    /**
     * Возвращаем данные если с фронта не приходит ID группы.
     *
     * @return array
     */
    protected function defaultGetData(): array
    {
        return [
            'name'      => 'Noname',
            'users'     => [],
            'corp_books' => [],
            'group_id'  => 0,
            'timeon'    => "00:00",
            'timeoff'   => "00:00",
            'plan'      => 0,
            'zoom_link' => '',
            'bp_link'   => '',
            'dialer_id' => null,
            'script_id' => null,
            'talk_minutes'  => null,
            'talk_hours'    => null,
            'quality'       => 'local',
            'time_exceptions'   => [],
            'time_address'      => 0,
            'workdays'          => 6,
            'editable_time'     => 0,
            'paid_internship'   => 0,
            'show_payment_terms' => 0,
            'groups'            => $this->profileGroupRepository->getGroupsIdNameWithPluck(true),
            'archived_groups'   => $this->profileGroupRepository->getGroupsIdNameWithPluck(),
        ];
    }
}