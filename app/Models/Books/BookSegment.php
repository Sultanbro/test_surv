<?php

namespace App\Models\Books;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookSegment extends Model
{
    use HasFactory;

    protected $table = 'book_segments';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'book_id',
        'page_start',
        'page_end',
        'pass_grade'
    ];

    /**
     * @return 
     */
    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }
}
