<?php

namespace App\Models\Books;

use Illuminate\Database\Eloquent\Model;

use App\ProfileGroup;
use App\Models\Books\Book;

class BookGroup extends Model
{
    protected $table = 'book_groups';

    public $timestamps = true;

    protected $fillable = [
        'name',
    ];

    const NAME_EXISTS = 'Выберите другое название, так как оно уже существует!';
    
    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }

    public function books()
    {
        $user_id = auth()->id();
        return $this->hasMany('App\Models\Books\Book', 'group_id', 'id')
            ->with('questions');
    }
    
    public static function getBooks($group_id)
    {   
        $group = BookGroup::find($group_id);

        if($group) {
            $book_ids = json_decode($group->books);
            return Book::whereIn('id', $book_ids)->get();
        } else {
            return null;
        }
    }

    public static function getBooksArray($group_id) {
        $group = BookGroup::find($group_id);

        if($group) {
            return json_decode($group->books);
        } else {
            return [];
        }
    }

    public static function addBooks($group_id, $books) {

        $group = BookGroup::find($group_id);

        if($group) {
            $book_ids = json_decode($group->books);
            foreach($books as $book) {
                array_push($book_ids, $book['id']); 
            }
            $book_ids = array_unique($book_ids);
            // sort($book_ids);
        
            $group->books = json_encode($book_ids);
            $group->save();
        }

    }


    public static function createGroup($name)
    {
        //check if 
        $isNameRegistered = self::where('name', $name)->first();
        if($isNameRegistered) {
            return self::NAME_EXISTS;
        }
        self::create([
            'name' => $name,
            'books' => json_encode([]),
        ]);
    }


    

}
