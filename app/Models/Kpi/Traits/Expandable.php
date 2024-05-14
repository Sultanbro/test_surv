<?php

namespace App\Models\Kpi\Traits;

trait Expandable
{
    /**
     * Для vue js
     */
    public function getExpandedAttribute() 
    {
        return false;
    }
}