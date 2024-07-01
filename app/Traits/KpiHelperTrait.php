<?php

namespace App\Traits;

trait KpiHelperTrait
{
    public int $user_id          = 1;
    public int $profile_group_id = 2;
    public int $position_id      = 3;

    /**
     * @param int $targetId
     * @return string|void
     */
    public function getModel(int $targetId)
    {
        switch ($targetId) {
            case $targetId == $this->user_id:
                return 'App\User';
            case $targetId == $this->profile_group_id:
                return 'App\ProfileGroup';
            case $targetId == $this->position_id:
                return 'App\Position';
        }
    }
}