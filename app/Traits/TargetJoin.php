<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TargetJoin
{
    abstract static public function query();

    public static function targetJoins(): Builder
    {
        $query = self::query();
        $table = $query->getModel()->getTable();

        return $query->select(
            $table . '.*'
        )
            ->leftJoin('users as u', function ($join) use ($table) {
                $join->on('u.id', '=', "$table.targetable_id")
                    ->where("$table.targetable_type", '=', 'App\User');
            })
            ->leftJoin('profile_groups as pg', function ($join) use ($table)  {
                $join->on('pg.id', '=', "$table.targetable_id")
                    ->where("$table.targetable_type", '=', 'App\ProfileGroup');
            })
            ->leftJoin('position as p', function ($join) use ($table)  {
                $join->on('p.id', '=', "$table.targetable_id")
                    ->where("$table.targetable_type", '=', 'App\Position');
            })
            ->leftJoin('users as updater', 'updater.id', '=', "$table.updated_by")
            ->leftJoin('users as creator', 'creator.id', '=', "$table.created_by");
    }
}