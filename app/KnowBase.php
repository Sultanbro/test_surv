<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\KnowBase;
use App\Models\KnowBaseModel;

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
        'access' // доступ   0 - никто, 1 - к просмотру,  2 - к редактированию
    ];


    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }
    
    public function children()
    {
        $user_id = auth()->id();
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('children','questions')
            ->with('item_model', function ($query) use ($user_id){
                $query->where('type', 3)
                    ->where('user_id', $user_id);
            });
    }

    public function item_model()
    {
        return $this->hasOne('App\Models\CourseItemModel', 'item_id');
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
                'id' => $child->id,
                'parent_id' => $child->parent_id,
                'title' => $child->title, 
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


    public  static function getRandomPage()
    {
        $corp_book_ids = self::getBooks(); // книги в группе
        if(count($corp_book_ids) == 0) return null;
       
        $books = KnowBase::
            where('text', '!=' ,'')
            ->whereIn('id', $corp_book_ids)
            ->get();

        return $books->count() > 0 ? $books->random() : null;  
    }

    public  function getUsersWithAccess()
    {
        $items = \App\Models\KnowBaseModel::where('book_id',$this->id)->get();
        
        $arr = [];

        foreach ($items as $key => $item) {

            if($item->model_type == 'App\\User') {
                $arr[] = $item->model_id;
            }

            if($item->model_type == 'App\\ProfileGroup') {
                $group = \App\ProfileGroup::find($item->model_id);
                if(!$group) continue;
                $arr = array_merge($arr, json_decode($group->users));
            }

            if($item->model_type == 'App\\Position') {
                $users = \App\User::where('position_id', $item->model_id)->get('id')->pluck('id')->toArray();
                $arr = array_merge($arr, $users);
            }

        }

        return array_unique($arr); 
    }

    private static function getBooks($access = 0) {
        
        $books = [];
        if(auth()->user()->is_admin == 1)  {
            $books = KnowBase::get('id')->pluck('id')->toArray();
        } else {

            $groups = auth()->user()->inGroups();
            $group_ids = collect($groups)->pluck('id')->toArray();
            $position_id =  auth()->user()->position_id;
            $user_id =  auth()->id();

            $up = KnowBaseModel::
                where(function($query) use ($group_ids, $access) {
                    $query->where('model_type', 'App\\ProfileGroup')
                        ->whereIn('model_id', $group_ids);
                    if($access == 2) $query->where('access', 2);
                })
                ->orWhere(function($query) use ($position_id, $access) {
                    $query->where('model_type', 'App\\Position')
                        ->where('model_id', $position_id);
                    if($access == 2) $query->where('access', 2);
                })
                ->orWhere(function($query) use ($user_id, $access) {
                    $query->where('model_type', 'App\\User')
                        ->where('model_id', $user_id);
                    if($access == 2) $query->where('access', 2);
                });

            $up = $up->get('book_id')
                ->pluck('book_id')
                ->toArray();
               
            $books = array_merge($books, $up);
            
           
            $books_with_read_access =  KnowBase::withTrashed()
                ->whereIn('access', $access == 2 ? [2] : [1,2])
                ->get('id')->pluck('id')
                ->toArray();
                
            $books = array_merge($books, $books_with_read_access);
        }
   
            
        return $books;
    }
    
}
