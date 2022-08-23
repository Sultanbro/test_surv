<?php

namespace App\Models;

use App\Models\Kpi\Traits\Expandable;
use App\Models\Kpi\Traits\Targetable;
use App\Models\Kpi\Traits\WithCreatorAndUpdater;
use App\Models\Kpi\Traits\WithActivityFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuartalPremium extends Model
{
    use HasFactory, SoftDeletes, Targetable, WithCreatorAndUpdater, WithActivityFields, Expandable; 

    protected $table = 'quartal_premiums';

    protected $appends = ['target', 'group_id', 'source', 'expanded'];
    
    protected $casts = [
        'created_at'  => 'date:d.m.Y H:i',
        'updated_at'  => 'date:d.m.Y H:i',
    ];

    protected $fillable = [
        'targetable_id',
        'targetable_type',
        'activity_id',
        'title',
        'text',
        'plan',
        'from',
        'to',
        'sum',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    

}
