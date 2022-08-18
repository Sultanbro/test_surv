<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Position extends Model
{
    use HasRoles;
    
    public $timestamps = true;

    protected $guard_name = 'web';

    protected $table = 'position';

    protected $fillable = [
        'book_groups',
        'position',
        'indexation', // Ведется ли индексация в течение одного года
        'sum', // Сумма
    ];

    const OPERATOR_ID = 32;
    const INTERN_ID = 47;
    
}
