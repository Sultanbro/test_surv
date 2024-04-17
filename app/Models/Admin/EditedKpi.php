<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int amount
 */
class EditedKpi extends Model
{
    protected $table = 'edited_kpi';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'author_id',
        'date',
        'amount',
        'comment', // why was edited
        'final', // last edit Auto or Manual. Manual has priority
    ];

    CONST AUTO = 0;
    CONST MANUAL = 1;
}
