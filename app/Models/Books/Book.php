<?php

namespace App\Models\Books;

use Illuminate\Database\Eloquent\Model;

use App\Models\Books\BookGroup;

class Book extends Model
{
    protected $table = 'books';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'group_id',
        'author',
        'link',
        'img',
    ];

    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }
    
    public static function withGroups(int $group_id = 0) {
        $books = Book::all();
 
        foreach($books as $book) {

            $_groups = [];

            if($group_id != 0) {
                $bookgroup = BookGroup::find($group_id);
                
                if($bookgroup) {
                    $bg_books = BookGroup::getBooksArray($bookgroup->id);
                    if(in_array($book->id, $bg_books)) {
                        array_push($_groups, $bookgroup->id);  
                    }
                }

            } else {

                $bookgroups = BookGroup::all();
            
                foreach($bookgroups as $bg) {
                    $bg_books = BookGroup::getBooksArray($bg->id);
                    
                    if(in_array($book->id, $bg_books)) {
                        array_push($_groups, $bg->id);  
                    }
                }

            }
            
            $book->groups = $_groups;
            $book->order = null;
            $book->actions = $book->id;
        }
        return $books;
    }

    public static function createBook($title, $author, $link)
    {
        $id = self::create([
            'title' => $title,
            'author' => $author,
            'link' => $link,
        ])->id;
        return $id;
    }

    public static function editBook($id, $title, $author, $link)
    {   
        $status = [];
        $book = self::find($id);
        if($book) {
            $book->update([
                'title' => $title,
                'author' => $author,
                'link' => $link,
            ]); 
            $status['code'] = 1;
            $status['message'] = 'Изменения успешно сохранены!';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Не удалось сохранить изменения. Книга не найдена!';
        }
        return $status;
    }

    public function getGroup() {
        
    }

    public static function getBookTitle($book_id) {
        $book = self::find($book_id);
        if($book) {
            $title = $book->author.' - '.$book->title;
        } else {
            $title = 'Книга не найдена! Исправьте ошибку!';
        }
        return $title;
    }

    public static function getLink($book_id) {
        $book = self::find($book_id);
        if($book) {
            $link = $book->link;
        } else {
            $link = 'Нет ссылки';
        }
        return $link;
    }


}
