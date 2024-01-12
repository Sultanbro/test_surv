<?php

namespace App\Traits;

use App\Events\TrackKpiUpdatesEvent;
use App\Models\Kpi\Kpi;
use App\Models\QuartalPremium;
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
        $query = static::query();

        if (static::class == QuartalPremium::class) {
            $query = $query->withoutGlobalScope(ActiveScope::class);
        }

        $model  = $query->findOrFail($id);

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
