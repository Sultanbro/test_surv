<?php

namespace App\Traits;

use App\Events\TrackKpiUpdatesEvent;
use App\Kpi;
use App\Models\Scopes\ActiveScope;
use Exception;;

trait ActivateAbleModelTrait
{
    abstract public static function query();

    /**
     * @param int $id
     * @param bool $status
     * @return bool
     * @throws Exception
     */
    public static function setActive(
        int $id,
        bool $status
    ): bool
    {
        dd(static::class, static::withoutGlobalScope(ActiveScope::class)->get());
        $model  = self::query()->findOrFail($id);

        if ($model->is_active == $status)
        {
            throw new Exception("Already on this status");
        }
        $model->update([
            'is_active' => $status
        ]);

        if ($model instanceof Kpi) {
            event(new TrackKpiUpdatesEvent($id));
        }

        return true;
    }
}
