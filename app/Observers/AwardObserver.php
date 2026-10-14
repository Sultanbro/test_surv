<?php

namespace App\Observers;

use App\Models\Award\Award;

class AwardObserver
{
    public function deleted(Award $award)
    {
        $award->users()->detach();
        $award->courses()->detach();
    }
}
