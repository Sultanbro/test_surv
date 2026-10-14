<?php

namespace App\Observers;

use App\Models\Award\Award;
use App\Models\Award\AwardCategory;

class AwardCategoryObserver
{
    public function deleted(AwardCategory $awardCategory)
    {
        $awardCategory->awards()->delete();
    }
}
