<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class DecompositionItem extends Model
{
    protected $table = 'decomposition_items';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'group_id',
    ];
    
}
