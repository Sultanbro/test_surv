<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\User;

class SalaryApproval extends Model
{
    protected $table = 'salary_approvals';

    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'user_id',
        'date',
    ];
}
