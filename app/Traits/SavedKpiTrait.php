<?php

namespace App\Traits;

use App\Models\Analytics\UpdatedUserStat;
use App\Repositories\SavedKpiRepository;
use App\User;
use Carbon\Carbon;
use Exception;

trait SavedKpiTrait
{
    /**
     * @param UpdatedUserStat $updateUserStat
     * @return void
     * @throws Exception
     */
    public function updateOrCreate(UpdatedUserStat $updateUserStat)
    {
        try {
            $user   = User::query()->findOrFail($updateUserStat->user_id);
            $carbon = Carbon::parse($updateUserStat->date);
            $total  = $this->service->calculateStat($user, $carbon);
            (new SavedKpiRepository)->updateOrCreate($user, $carbon, $total);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}