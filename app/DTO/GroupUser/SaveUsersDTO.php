<?php
declare(strict_types=1);

namespace App\DTO\GroupUser;

final class SaveUsersDTO
{
    /**
     * @param int $groupId
     * @param array $users
     * @param array $groupInfo
     * @param int $dialerId
     * @param int $scriptId
     * @param int $talkHours
     * @param int $talkMinutes
     */
    public function __construct(
        public int $groupId,
        public array $users,
        public array $groupInfo,
        public int $dialerId,
        public int $scriptId,
        public int $talkHours,
        public int $talkMinutes
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group_id' => $this->groupId,
            'users' => $this->users,
            'group_info' => $this->groupInfo,
            'dialer_id' => $this->dialerId,
            'script_id' => $this->scriptId,
            'talk_hours' => $this->talkHours,
            'talk_minutes' => $this->talkMinutes
        ];
    }
}