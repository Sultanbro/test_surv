<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App\UserDescription;
use App\BookCategory;

class Book extends Model
{
    protected $connection= 'bpartners_db';

    protected $table = 'books';

    public $timestamps = false;


    public  static function getRandomPage()
    {
    
    $corp_books = [];
      $groups = Auth::user()->inGroups();
      foreach ($groups as $group) {
        $corp_books = array_merge($corp_books, json_decode($group->corp_books));
      }
      
      $corp_books = array_unique($corp_books); // книги в группе

      $ud = UserDescription::where('user_id', Auth::user()->id)->first(); // или лично настроенные в профиле
      if($ud && count(json_decode($ud->books)) > 0) {
        $corp_books = json_decode($ud->books);
      } 

      $tree = BookCategory::whereIn('id', $corp_books)->where('is_deleted', 0)->orderBy('queue_number')->get();
      
      
      $bc = new BookCategory();
      $categories = $bc->getAll($tree);
   
      $categories_id = [];
      foreach($categories as $cat) $categories_id[] = $cat->id;

      $book = self::whereIn('category_id', $categories_id)
        ->where('is_deleted', 0)
        ->orderBy('queue_number')
        ->get()
        ->random();
      
        return $book;
        
    }
}
