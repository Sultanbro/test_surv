<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTax extends Model
{
    use HasFactory;

    protected $table = 'user_taxes';

    const ACTIVE = 1;
    const REMOVED = 0;

    protected $fillable = [
        'tax_group_id', 'user_id', 'status', 'from', 'to'
    ];
}
