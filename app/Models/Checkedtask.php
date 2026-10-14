<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkedtask extends Model
{
    use SoftDeletes;
    protected $table = 'checkedtasks';

    public $timestamps = true;
    protected $softDelete = true;

    protected $fillable = [
        'task_id',
        'url',
        'created_date',
        'checked',
        'user_id'
    ];

    public function task(){
        return $this->belongsTo(Task::class)->withTrashed();
    }

}
