<?php

namespace App\Http\Controllers\Learning;

use Illuminate\Http\Request;
use App\KnowBase;
use App\Http\Controllers\Controller;

/** Just for remote link  for corp book page */
class LinkController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function opened_corp_book(Request $request) { 

        $book = KnowBase::where('hash', $request->id)->first();
      
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
