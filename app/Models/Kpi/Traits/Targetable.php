<?php

namespace App\Models\Kpi\Traits;

trait Targetable
{
    /**
     * models
     */
    public array $targets = [
        'App\User' => 1,
        'App\ProfileGroup' => 2,
        'App\Position' => 3,
    ];

    /**
     * Таргет
     * @return array | null
     */
    public function getTargetAttribute(): ?array
    {
        $target = $this->targetable;
        if (!$target) return null;

        $type = $this->targets[$this->targetable_type];

        if ($type == 1 && $target) $name = $target->last_name . ' ' . $target->name;
        if ($type == 2 && $target) $name = $target->name;
        if ($type == 3 && $target) $name = $target->position;

        return $target ? [
            'id' => $this->targetable_id,
            'name' => $name,
            'type' => $type,
        ] : null;
    }

    /**
     * Get the parent targetable model (user, group, position).
     */
    public function targetable()
    {
        return $this->morphTo()
            ->withTrashed();
    }
}
