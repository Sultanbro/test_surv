<?php

namespace App\Models\Admin\Headhunter;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{   
    protected $table = 'headhunter_vacancies';

    public $timestamps = true;

    const OPEN = 1;
    const CLOSED = 0;
    
    protected $fillable = [
        'vacancy_id',
        'title',
        'manager_id',
        'city',
        'status',
        'from'
    ];
}
