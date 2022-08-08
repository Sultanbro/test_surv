<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use SoftDeletes;

    protected $table = 'checklists';

    public $timestamps = true;

    /*protected $casts = [
        'active_check_text' => 'array',
    ];*/
    protected $softDelete = true;

    protected $fillable = [
        'creator_id',
        'title',
        'show_count',
        'json_users',
    ];

    public function tasks(){
        return $this->hasMany(Task::class,'checklist_id','id');
    }

    public function users(){
        return $this->belongsToMany(\App\User::class);
    }

    public function creator(){
        return $this->belongsTo(\App\User::class, 'creator_id');
    }
    /*protected $fillable = [
        'title',
        'auth_id',
        'auth_name',
        'auth_last_name',
        'count_view',
        'item_type',
        'item_id',
    ];*/



}
