<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Book;
use App\KnowBase;

/** Just for remote link  for corp book page */
class LinkController extends Controller
{
  
    public function index()
    {
        return view('home');
    }

    public function opened_corp_book($hash) {

        $book = KnowBase::where('hash', $hash)->first();
      
        if($book) {
          $title = $book->title;
          $text = $book->text;
        } else {
          abort(404);
        }
        
        return view('admin.corp_book.opened_corp_book')->with([
          'title' => $title,
          'text' => $text
        ]);
      }
  
}
