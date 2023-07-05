<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class AnalyticsActivitiesSetting extends Model
{
    use HasFactory;

    const COLUMN_PREFIX = "group_";

    protected $guarded = [];

    protected $table = "analytics_activities_settings";
}
