<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;

interface SavedKpiInterface
{
    public function getSavedKpiForMonth(User $user, Carbon $carbon);
}