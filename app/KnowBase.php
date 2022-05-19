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
        'editor_id', // 
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

    public static function getArray(&$arr, $kb) {
        foreach ($kb->children as $key => $child) {
            array_push($arr, [
                'parent_id' => $child->parent_id,
                'title' => $child->parent_id, 
                'user_id'=> $child->user_id, 
                'editor_id'=> $child->editor_id, 
                'text'=> $child->text, 
                'is_deleted'=> $child->is_deleted, 
                'order'=> $child->order, 
                'hash'=> $child->hash, 
            ]);
            
            self::getArray($arr, $child);
        }
    }
    
}
