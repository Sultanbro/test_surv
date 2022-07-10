<?php

namespace App\Models\Books;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Position;
use App\ProfileGroup;
use App\Models\Books\Book;
use App\Models\Books\BookGroup;

class BookResult extends Model
{
    protected $table = 'book_results';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'book_id',
    ];


    public static function getUnreadBook($user_id)
    {   
        $read_book_ids = [];
        $unread_book_ids = [];
        
        $user = User::find($user_id);   
        if(!$user) {return null;}

        $read_books = self::select('book_id')->where('user_id', $user_id)->get(); // прочитанные книги
        
        foreach($read_books as $read_book) {
            array_push($read_book_ids, $read_book->book_id); // push to array TODO

        }


        ////////////////////
        
        $groups_array = $user->inGroups();
        
        $book_groups = [];
        foreach($groups_array as $_group) {
            $_book_groups = ProfileGroup::getBookGroupsArray($_group->id);
            $book_groups = array_merge($book_groups, $_book_groups);
        }
        
        $book_groups = array_unique($book_groups);
        
        //dd($book_groups);
        
        foreach($book_groups as $group_id) {
            $unread_book_ids = array_merge($unread_book_ids, BookGroup::getBooksArray($group_id));
        }

        $unread_book_ids = array_unique($unread_book_ids); // все книги, которые нужно прочитать

        $unread_book_ids = array_diff($unread_book_ids, $read_book_ids); // непрочитанные книги

        if(count($unread_book_ids) != 0) {
            return array_values($unread_book_ids)[0];
        } else {
            return null;
        }
    }

    public static function setBookRead($user_id, $book_id) {
        $record = self::where([
            'user_id' => $user_id,
            'book_id' => $book_id,
        ])->first();
        
        if(!$record) {
            $record = self::create([
                'user_id' => $user_id,
                'book_id' => $book_id,
            ]);
        }
    }

    public static function setBookNotRead($user_id, $book_id) {
        $record = self::where([
            'user_id' => $user_id,
            'book_id' => $book_id,
        ])->first();
        
        if($record) $record->delete();
    }
}
