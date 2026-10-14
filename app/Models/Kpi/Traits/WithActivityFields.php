<?php

namespace App\Models\Kpi\Traits;

trait WithActivityFields
{

    public function getGroupIdAttribute()
    {
        return 0;
    }

    public function getSourceAttribute()
    {
        return 0;
    }
    
}