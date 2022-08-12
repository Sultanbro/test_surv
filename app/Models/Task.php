<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Task extends Model
{
    use SoftDeletes;
    protected $table = 'tasks';

    public $timestamps = true;
    protected $softDelete = true;

    protected $fillable = [
        'task',
        'checklist_id',
    ];

    public function checkedtasks(){
        return $this->hasMany(Checkedtask::class,'task_id','id')->whereDate('created_at', Carbon::today())->where('user_id',auth()->id());
    }




}
