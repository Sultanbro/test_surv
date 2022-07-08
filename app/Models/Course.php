<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseItem;

class Course extends Model
{
    protected $table = 'courses';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'user_id',
        'img',
        'text',
    ];

    public function items()
    {
        return $this->hasMany(CourseItem::class, 'course_id')->orderBy('order');
    }

    public function items_models()
    {
        $user_id = auth()->user()->id;
        return $this->hasMany(CourseItem::class, 'course_id')->orderBy('order')->with('elements', function ($query) use ($user_id){
            $query->where('user_id', $user_id);
        });
    }

    

    /**
     * Найти точку, где остановились при прохождении курса
     * 
     * @param Collection $items CourseItem
     * 
     * @return Collection
     */
    public function setCheckpoint($items)
    {
        $disabled = false;
        foreach ($items as $key => $item) {
            if(!$disabled) {
                $item->status = $item->countItems() <= $item->elements->count() ? CourseResult::COMPLETED : CourseResult::ACTIVE;
                if($item->status == CourseResult::ACTIVE) $disabled = true;
            } else {
                $item->status = CourseResult::INITIAL;
            }
        }

        return $items;
    }
}
