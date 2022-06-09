<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Models\Books\BookGroup;

class Position extends Model
{
    public $timestamps = true;

    protected $table = 'position';

    protected $fillable = [
        'book_groups',
        'position',
        'indexation', // Ведется ли индексация в течение одного года
        'sum', // Сумма
    ];


    public static function getPositionsArray() {
        $query = Position::all();
        $positions = [];
        foreach($query as $position) {
            array_push($positions, $position->position); 
        }
        return $positions;
    }
}
