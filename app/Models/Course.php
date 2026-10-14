<?php

namespace App\Models;

use App\Models\Award\Award;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;
    
    protected $table = 'courses';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'user_id',
        'award_id',
        'img',
        'text',
        'order',
        'points', // amount of bonuses in course
        'stages' // all stages in course
    ];


    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
    public function courseAwards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class, 'award_course', 'course_id', 'award_id')
            ->withPivot(['user_id', 'path', 'format'])->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(CourseItem::class, 'course_id')->orderBy('order');
    }   

    public function models()
    {
        return $this->hasMany(CourseModel::class, 'course_id');
    }

    public function course_results()
    {
        return $this->hasMany('App\Models\CourseResult', 'course_id', 'id');
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

                // get completed stages
                if($model) {
                    $model_ids = $model->getOrder();
                
                    $cim = CourseItemModel::where('user_id', auth()->id())
                        ->where('type', CourseItemModel::getType($item->item_model))
                        ->where('course_item_id', $item->id)
                        ->whereIn('item_id', $model_ids)
                        ->select('item_id')
                        ->get();

                    $completed_stages = $cim->count();
                }
                
                // can replace $item->countItems() with count($item->model()->getOrder())
               
                // if(CourseItemModel::getType($item->item_model) == 1) { 
                //     dump($model->getOrder());
                //         dump((int)$item->countItems() <= (int)$completed_stages);
                //     dump($item->countItems());
                //     dump((int)$completed_stages);
                // }
                
                $count = (int)$item->countItems();
                $item->all_stages = $count;
                $item->completed_stages = $completed_stages;
                $item->status = $count <= (int)$completed_stages ? CourseResult::COMPLETED : CourseResult::ACTIVE;
                
                // dump('not found actuve');
                // dump($item->item_model . ' - ' . CourseItemModel::getType($item->item_model));
                // dump($completed_stages  . ' from ' . $count);

                // found active
                if($item->status == CourseResult::ACTIVE) {
                    $found_active = true;

                    $diff = array_diff($model_ids, $cim->pluck('item_id')->toArray());
                    $diff = array_values($diff);
 
                    // set checkpoint
                    if(count($diff) > 0) {
                        // get id
                       
                       
                        //if(count($diff) > 0) $item->last_item = $item->getNextElement($diff[0]);
                      $item->last_item = $diff[0];
                    }
                    
                    
                }

            } else {

                $model = $item->model();
                $completed_stages = 0;

                // get completed stages
                if($model) {
                    $model_ids = $model->getOrder();
                
                    $cim = CourseItemModel::where('user_id', auth()->id())
                        ->where('type', CourseItemModel::getType($item->item_model))
                        ->whereIn('item_id', $model_ids)
                        ->select('item_id')
                        ->get();

                    // dump('active');
                    // dump($item->item_model . ' - ' . CourseItemModel::getType($item->item_model));
                    // dump($completed_stages  . ' from ' . $count);

                    $completed_stages = $cim->count();
                }
                
                $count = (int)$item->countItems();
                $item->all_stages = $count;
                $item->completed_stages = $completed_stages;
                $item->status = CourseResult::INITIAL;
            }

        }


        return $items;
    }
}
