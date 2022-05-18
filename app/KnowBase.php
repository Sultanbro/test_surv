<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowBase extends Model
{   
    use SoftDeletes;
    
    protected $table = 'kb';

    public $timestamps = true;

    protected $appends = ['opened'];
    
    protected $fillable = [
        'parent_id',
        'title', 
        'user_id', // author 
        'text', 
        'is_deleted', 
        'order', 
        'hash', // уникальная ссылка чтобы поделиться
    ];


    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }
    
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order')->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getOpenedAttribute()
    {
        return false;
    }
    
}
