<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $connection= 'bpartners_db';

    protected $table = 'category';
    public $timestamps = false;

    protected $appends = ['opened'];

    public function books()
    {
        return $this->hasMany('App\Book', 'category_id', 'id');
    }

    public function categoryes()
    {
        return $this->hasMany('App\BookCategory', 'parent_cat_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_cat_id')->with('children');
    }
    
    public function getOpenedAttribute()
    {
        return $this->parent_cat_id == null ? true : false;
    }
    
    public function parent()
    {
        return $this->belongsTo('App\BookCategory', 'parent_cat_id');
    }

    public function getAll($categories)
    {
       
        $append = collect();
        foreach ($categories as $category)
        {
           
            if ($category->categoryes()->count())
            {  
                $append = $append->merge($category->categoryes()->notDeleted()->get());               
            }           
            
        }
       
        if ($append->count())
        {
            $append = $this->getAll($append);
        }
  
        return $categories->merge($append);
    }

    public static function gedAll($categories)
    {
       
        $append = collect();
        foreach ($categories as $category)
        {
           
            if ($category->categoryes()->count())
            {  
                $append = $append->merge($category->categoryes()->notDeleted()->get());               
            }           
            
        }
       
        if ($append->count())
        {
            $append = self::getAll($append);
        }
  
        return $categories->merge($append);
    }

    public function scopeNotDeleted($query){
        return $query->where('is_deleted', 0);
    }
}
