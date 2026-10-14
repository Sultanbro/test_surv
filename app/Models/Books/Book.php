<?php

namespace App\Models\Books;

use App\Contracts\CourseV2Interface;
use Illuminate\Database\Eloquent\Model;

use App\Contracts\CourseInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model implements CourseInterface, CourseV2Interface
{
    use SoftDeletes;

    protected $table = 'books';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'group_id',
        'author',
        'description',
        'link',
        'img',
        'domain',
    ];

    /**
     * @return
     */
    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }

    /**
     * @return
     */
    public function segments()
    {
        return $this->hasMany('App\Models\Books\BookSegment', 'book_id')->orderBy('page_start', 'asc');
    }

    public function item_model()
    {
        return $this->hasOne('App\Models\CourseItemModel', 'item_id');
    }

    public function countAllStages()
    {
        return $this->segments()->count();
    }

    /**
     * @param int $group_id
     *
     * @return \Illuminate\Database\Eloquent\Collection;
     */
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

    /**
     * @param mixed $title
     * @param mixed $author
     * @param mixed $link
     *
     * @return int
     */
    public static function createBook($title, $author, $link)
    {
        $id = self::create([
            'title' => $title,
            'author' => $author,
            'link' => $link,
        ])->id;
        return $id;
    }

    /**
     * @param mixed $id
     * @param mixed $title
     * @param mixed $author
     * @param mixed $link
     *
     * @return array
     */
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

    /**
     * @param mixed $book_id
     *
     * @return String
     */
    public static function getBookTitle($book_id) {
        $book = self::find($book_id);
        if($book) {
            $title = $book->author.' - '.$book->title;
        } else {
            $title = 'Книга не найдена! Исправьте ошибку!';
        }
        return $title;
    }

    /**
     * @param mixed $book_id
     *
     * @return String
     */
    public static function getLink($book_id) {
        $book = self::find($book_id);
        if($book) {
            $link = $book->link;
        } else {
            $link = 'Нет ссылки';
        }
        return $link;
    }

    /**
     * CourseInterface
     * @return array
     */
    public function getOrder()
    {
        return BookSegment::where('book_id', $this->id)
            ->get('id')
            ->pluck('id')
            ->toArray();
    }

    /**
     * CourseInterface
     * @param mixed $id
     *
     * @return [type]
     */
    public function nextElement($id)
    {
        $arr = $this->getOrder();
        $key = array_search($id, $arr);
        return $key && $key + 1 <= count($arr) - 1 ? $arr[$key + 1] : null;
    }

}
