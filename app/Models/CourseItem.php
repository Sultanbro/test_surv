<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\Models\Videos\Video;
use App\KnowBase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CourseItem extends Model
{

    use SoftDeletes;

    protected $table = 'course_items';

    public $timestamps = true;

    protected $fillable = [
        'course_id',
        'item_id',
        'item_model',
        'order',
        'title',
        'items',
    ];

    // item_model
    CONST MODEL_VIDEO = 'video';
    CONST MODEL_KB = 'kb';
    CONST MODEL_BOOK = 'book';

    /**
     * @return HasMany
     */
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function courseable() {
        return $this->morphTo();
    } 

    public function element() {
        return $this->morphTo('element', 'item_model', 'item_id');
    } 

    public function isVideo() {
        return $this->item_model == 'App\Models\Videos\VideoPlaylist';
    }

    public function isBook() {
        return $this->item_model == 'App\Models\Books\Book';
    }

    public function isKnowbase() {
        return $this->item_model == 'App\KnowBase';
    }

    /**
     * NUmber of elements in CourseItem
     */
    public function countItems()
    {   
        $model = $this->model();
        return $model ? count($model->getOrder()) : 0;
    }

    /**
     * Count bonuses from test questions
     * of course_item models 
     */
    public function countBonuses()
    {   
        $model = $this->model();
        
        $points = 0;

        if($model) {

            /**
             * find morph table to test_questions
             */
            if($this->isBook()) {
                $model_name = 'App\Models\Books\BookSegment';
            } else if($this->isVideo()) {
                $model_name = 'App\Models\Videos\Video';
            } else if($this->isKnowbase()) {
                $model_name = 'App\Models\Books\Booksegment';
            } else {
                throw new \Exception('Model not found for counting bonuses');
            }
            
            /**
             * count total points
             */
            $points = \App\Models\TestQuestion::whereIn('testable_id', $model->getOrder())
                ->where('testable_type', $model_name)
                ->get()
                ->sum('points');
        }

        return $points;
    }

    /**
     * Next element to last id
     * @param int $last_id
     * 
     * @return int id
     */
    public function getNextElement($last_id)
    {
        $item = $this->model();
        return $item ? $item->nextElement($last_id) : null;
    }

    /**
     * Return model by item_model
     * 
     * @return Model
     */
    public function model()
    {
        $item = null;

        if($this->isVideo()) {
            $item = VideoPlaylist::find($this->item_id);
        }

        if($this->isBook()) {
            $item = Book::find($this->item_id);
        }

        if($this->isKnowbase()) {
            $item = KnowBase::find($this->item_id);
        }

        return $item ?? null;
    }

}
