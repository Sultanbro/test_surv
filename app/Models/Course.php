<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseItem;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\Models\Videos\Video;

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

    /**
     * Найти точку, где остановились при прохождении курса
     * 
     * @param Collection $items CourseItem
     * 
     * @return Collection
     */
    public function setCheckpoint($items)
    {
        $found_active = false;
        foreach ($items as $key => $item) {

            $item->last_item = null;

            if(!$found_active) {

                $arr = [];
                $model_ids = [];

                $model = $item->model();
                $completed_stages = 0;

                if($model) {
                    $model_ids = $model->getOrder();

                    $cim = CourseItemModel::where('user_id', auth()->id())
                        ->where('type', CourseItemModel::getType($item->item_model))
                        ->whereIn('item_id', count($model_ids))
                        ->select('item_id')
                        ->get();

                    $completed_stages = $cim->count();
                }
                
                // can replace $item->countItems() with count($item->model()->getOrder())
                $item->status = $item->countItems() <= $completed_stages ? CourseResult::COMPLETED : CourseResult::ACTIVE;

                if($item->status == CourseResult::ACTIVE) {
                    $found_active = true;
                    
                    // get id
                    $diff = array_diff($assigned, $model_ids);
                    $diff = array_values($diff);
            
                    if(count($diff) > 0) $item->last_item = $item->getNextElement($diff[0]);
                    
                }

            } else {

                $item->status = CourseResult::INITIAL;

            }

        }

        return $items;
    }
}
