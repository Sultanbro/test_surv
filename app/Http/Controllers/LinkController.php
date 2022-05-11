<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Book;

/** Just for remote link  for corp book page */
class LinkController extends Controller
{
  
    public function index()
    {
        return view('home');
    }

    public function opened_corp_book($d, $z, $hash) {

        $book = Book::where('hash', $hash)->first();
      
        if($book) {
          $title = $book->title;
          $text = $book->description;
        } else {
          abort(404);
        }
        
        return view('admin.corp_book.opened_corp_book')->with([
          'title' => $title,
          'text' => $text
        ]);
      }
  
}
