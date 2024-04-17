<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int amount
 */
class EditedBonus extends Model
{
    protected $table = 'edited_bonuses';

    public $timestamps = false;

    protected $fillable = [
        'author_id',
        'user_id',
        'date',
        'amount',
        'comment'
    ];
}
