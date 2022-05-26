<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckUsers extends Model
{
    protected $table = 'check_users';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'last_name',
        'check_list_id',
        'check_users_id',
        'check_reports_id',
        'count_view',
        'item_type',
        'item_id',
    ];


    public function saveDataBase()
    {

    }

    public function getReports(){

        return $this->hasMany('App\Models\Reports', 'check_users_id', 'check_users_id');
    }
}
