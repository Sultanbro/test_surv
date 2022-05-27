<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckReports extends Model
{
    use HasFactory;


    protected $table = 'check_reports';

    public $timestamps = true;



    protected $fillable = [
        'check_users_id',
        'check_id',
        'count_check',
        'count_check_auth',
        'date',
        'item_type',
        'item_id',
    ];
}
