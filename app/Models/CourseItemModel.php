<?php

namespace App\Models;

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

    CONST PASSED = 1;
    CONST NOT_PASSED = 0;

    const BOOK = 1;
    CONST VIDEO = 2;
    CONST KNOWBASE = 3;

    public function getModel()
    {
        if($this->type == 1) return 'App\Models\Books\Book';
        if($this->type == 2) return 'App\Models\Videos\Video';
        if($this->type == 3) return 'App\Knowbase';
    }

    public static function getType($model)
    {
        if($model == 'App\Models\Books\Book') return 1;
        if($model == 'App\Models\Videos\Video') return 2;
        if($model == 'App\Knowbase') return 3;
    }
    
}
