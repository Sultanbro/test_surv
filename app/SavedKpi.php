<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

/**
 * @property int $user_id
 * @property string $date
 * @property float $total
 */
class SavedKpi extends Model
{
    public $timestamps = false;

    protected $table = 'saved_kpi';

    protected $fillable = [
        'user_id',
        'date',
        'total',
    ];
}
