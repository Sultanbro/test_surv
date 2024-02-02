<?php

namespace App\Models;

use App\KnowBase;
use App\Models\Books\Book;
use App\Models\Videos\VideoPlaylist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseItemModel extends Model
{
    use HasFactory;

    protected $table = 'course_item_models';

    public $timestamps = true;

    protected $fillable = [

        // element id
        // but in book it represents page number with test
        'item_id',
        'type', // book video kb
        'status',
        'user_id',
        'course_item_id',
    ];

    // statuses
    CONST PASSED = 1;
    CONST NOT_PASSED = 0;

    // types
    const BOOK = 1;
    CONST VIDEO = 2;
    CONST KNOWBASE = 3;

    public function getModel()
    {
        if($this->type == 1) return 'App\Models\Books\BookSegment';
        if($this->type == 2) return 'App\Models\Videos\Video';
        if($this->type == 3) return 'App\Knowbase';
    }

    public static function getType($model)
    {
        if($model == 'App\Models\Books\Book') return 1;
        if($model == 'App\Models\Videos\VideoPlaylist') return 2;
        if($model == 'App\KnowBase') return 3;
    }

    /**
     * Прогресс пользователя на этапе курса
     *
     * $courseItemId, этап
     *
     * $element, источник этапа
     *
     * @return \Illuminate\Support\Collection
     */
    public function progress(int $user_id, int $courseItemId, KnowBase|VideoPlaylist|Book $element)
    {
        $elementChildren = $element->getOrder();
        $type = $this->getType( get_class($element) );

        return CourseItemModel::whereIn('item_id', $elementChildren)
                        ->where('course_item_id', $courseItemId)
                        ->where('type', $type)
                        ->where('user_id', $user_id)
                        ->get();
    }

}
