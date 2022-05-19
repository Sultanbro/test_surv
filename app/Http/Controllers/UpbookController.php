<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Books\Book;
use App\Models\Books\BookGroup;
use App\Models\TestQuestion;

class UpbookController extends Controller
{

    public function index(Request $request)
    {   
        View::share('menu', 'learning');
        View::share('link', 'upbook');

        return view('upbooks');
    }

    public function edit(Request $request)
    {   
        View::share('menu', 'upbook_edit');
        View::share('link', 'upbook');

        return view('admin.upbooks-edit');
    }

    public function admin_get(Request $request)
    {   

        $cats = BookGroup::get();

        $books_with_cats = [];
        foreach ($cats as $key => $cat) {
            $books_with_cats = array_merge($books_with_cats, json_decode($cat->books, true));
            $cat->books = Book::whereIn('id', json_decode($cat->books, true))->get();
        }

        $nocat_books = Book::whereNotIn('id', array_unique($books_with_cats))->get();

        if($nocat_books->count() > 0) {
            $cat = new BookGroup();
            $cat->id = 0;
            $cat->name = 'Без категории';
            $cat->books = $nocat_books;

            $cats->push($cat);
        }
       
        return [
            'categories' => $cats->toArray()
        ];
    }


    public function createCategory(Request $request)
    {
        return BookGroup::create([
            'name' => $request->name,
            'books' => '[]'
        ]);
    }

    public function deleteCategory(Request $request)
    {   
        $bg = BookGroup::find($request->id);
        if($bg) {
            $bg->delete();
        }
    }

    

    public function getTests(Request $request)
    {
        $qs = TestQuestion::where('testable_type', 'App\Models\Books\Book')->where('testable_id', $request->id)->get()->groupBy('page');

        $arr = [];
        foreach($qs as $id => $test) {
            array_push($arr, [
                'page' => $id,
                'pages' => $id,
                'questions' => $test 
            ]);
        }
        return ['tests' => $arr];
    }
    
    public function save(Request $request)
    {
        $b = Book::find($request->book['id']);
        if($b) {
            $b->title = $request->book['title'];
            $b->author = $request->book['author'];
            $b->save();

            $cat = BookGroup::find($request->cat_id);
            if($cat) {
                $books = json_decode($cat->books, true);
                array_push($books, $b->id);
                $cat->books = json_encode(array_unique($books));
                $cat->save();
            }
        }
    }

    public function update(Request $request)
    {
        $b = Book::find($request->book['id']);
        if($b) {
            $b->title = $request->book['title'];
            $b->author = $request->book['author'];
            $b->save();

            $cat = BookGroup::find($request->cat_id);
            if($cat) {
                $books = json_decode($cat->books, true);
                array_push($books, $b->id);
                $cat->books = json_encode(array_unique($books));
                $cat->save();
            }


            foreach ($request->tests as $key => $test) {

                foreach ($test['questions'] as $q) {
                    
                    $params = [
                        'order' => 0,
                        'page'=> $test['page'],
                        'points'=> $q['points'],
                        'testable_id'=> $b->id,
                        'testable_type'=> "App\Models\Books\Book",
                        'text'=> $q['text'],
                        'type'=> $q['type'],
                        'variants'=> $q['variants'],
                    ];

                    if($q['id'] != 0) {
                        $testq = TestQuestion::find($q['id']);
                        if($testq) $testq->update($params);
                    } else {
                        TestQuestion::create($params);
                    }
    
                }
                
            }
        }

        
    }
    
    
    
}
