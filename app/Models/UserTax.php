<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTax extends Model
{
    use HasFactory;

    protected $table = 'user_tax';

    const ACTIVE = 1;
    const REMOVED = 0;

    protected $fillable = [
        'user_id', 'tax_id', 'is_percent', 'end_subtraction', 'value', 'from', 'to', 'status'
    ];
}
