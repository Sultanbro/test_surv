<?php

namespace App\Traits;

use App\Events\TrackKpiUpdatesEvent;
use App\Kpi;
use App\Models\Kpi\Bonus;
use App\User;
use Exception;

use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\String\s;

trait ActivateAbleModelTrait
{
//    abstract public static function query();

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
        dd(static::class, static::find(13));
        $model  = static::query()->findOrFail($id);

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
