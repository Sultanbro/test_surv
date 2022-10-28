<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Books\BookGroup;
use App\User;
use Carbon\Carbon;

class ProfileGroupMigration extends Model
{
    protected $table = 'profile_group_migrations';

    public $timestamps = false;


    protected $fillable = [
        'group_id', // 
        'user_id', // 
        'enter', // Вход в отдел Дата
        'resign', // Выход из группы Дата
        'step', // Этап 
    ];

    // step
    CONST APPLIED = 1;
    CONST FIRED = 2;
    CONST WORKING = 0;
    
}
