<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class EditedSalary extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'date',
        'amount',
        'comment'
    ];



    
}
