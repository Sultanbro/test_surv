<?php

namespace App\Models\Kpi\Traits;

trait WithCreatorAndUpdater
{
     /**
     * Создатель
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
    
    /**
     * Кто изменил в последний раз
     */
    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }
}