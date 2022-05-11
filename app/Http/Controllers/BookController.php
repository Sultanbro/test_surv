<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Position;
use App\ProfileGroup;
use App\Models\Books\Book;
use App\Models\Books\BookGroup;
use App\Models\Books\BookResult;


class BookController extends Controller
{
    public function __construct()
    {
        View::share('title', 'Админ панель');
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Получить непрочитанную книгу user_id = 5
    // BookResult::getUnreadBook(5);

    // Установка книгу прочитанной у user_id = 5, book_id = 10
    // BookResult::setBookRead(5, 10);

    // Удалить книгу

    // Удалить группу

    public function index()
    {
         
        dd(BookResult::getUnreadBook(5));
    }


    public function books()
    {   
        return response()->json([
            'books' => Book::withGroups(),
            'groups' => BookGroup::get()->pluck('name', 'id')->toArray()
        ]);
    }
    
    public function positionGroups(Request $request) {

        if($request->position_id) {

            $position = Position::find($request->position_id);

            return response()->json([
                'groups' => Position::getBookGroups($request->position_id),
                'position_id' => $position ? $position->id : 0
            ]);
        } else {
            return response()->json([
                'groups' => BookGroup::all(),
                'positions' => Position::all()
            ]);
        }

        
    }

    public function savePositionGroups(Request $request) {
        Position::addGroupsToPosition($request->position, $request->groups);
    }
    
    public function group(Request $request)
    {   
        $group = BookGroup::find($request->group_id);
        $books = BookGroup::getBooks($request->group_id);
        foreach($books as $book) {
            $book->title = $book->title.' - '.$book->author;
        }

        return response()->json([
            'books' => $books,
            'group_id' =>  $group ? $group->id : 0
        ]);
    }

    public function groups()
    {   
        $books = Book::all();
        foreach($books as $book) {
            $book->title = $book->title.' - '.$book->author;
        }
        
        return response()->json([
            'groups' => BookGroup::all(),
            'books' => $books
        ]);
    }

    public function addBooksToGroup(Request $request) {
        BookGroup::addBooks($request['group_id'], $request['books']);
    }

    /**
     CREATE 
    */
    public function createBook(Request $request)
    {
        Book::createBook($request['title'], $request['author'], $request['link']);
        return response()->json(['success' => 'Книга успешно сохранена']);
    }

    public function createBookGroup(Request $request)
    {
        $book = BookGroup::createGroup($request['name']);
        if($book == BookGroup::NAME_EXISTS) {
            return response()->json(['message' => BookGroup::NAME_EXISTS]);
        }
    }

    /**  
     UPDATE 
     * */
    public function editBook(Request $request)
    {   
        $status = Book::editBook($request['id'], $request['title'], $request['author'], $request['link']);
        return response()->json($status);
    }

    /**
     DELETE
    */
    public function deleteBook(Request $request)
    {
        $book = Book::find($request['id']);
        $book->delete();
    }

    public function deleteGroup(Request $request)
    {
        $group = BookGroup::find($request['id']);
        $group->delete();
    }
}
