<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\Models\Videos\Video;
use App\KnowBase;

class CourseItem extends Model
{
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


    public function isVideo() {
        return $this->item_model == 'App\Models\Videos\Video';
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
        $item = $this->model();
        return $item ? $item->getOrder() : 0;
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
