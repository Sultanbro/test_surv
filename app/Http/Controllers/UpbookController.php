<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Books\Book;
use App\Models\Books\BookGroup;
use App\Models\TestQuestion;
use App\Models\Course;
use App\Models\CourseItemModel;

class UpbookController extends Controller
{

    public function index(Request $request)
    {   
        View::share('menu', 'upbook');
        View::share('link', 'upbook');

        return view('upbooks');
    }

    public function admin_get(Request $request)
    {   

        $cats = BookGroup::with('books')->get();

     
        $nocat_books = Book::where('group_id', 0)->get();

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
        $book = Book::find($request->id);
        $qs = TestQuestion::where('testable_type', 'App\Models\Books\Book')->where('testable_id', $request->id)->get()->groupBy('page');

        $arr = [];
        foreach($qs as $id => $test) {

            $_test = [];
            foreach ($test as $key => $q) {
                $q = $q->toArray();
                $q['result'] = 0;
                $_test[] = $q;
            }

            array_push($arr, [
                'page' => $id,
                'pages' => $id,
                'pass' => false,
                'questions' => $_test 
            ]);

            $_pages = array_column($arr, 'page');
            array_multisort($_pages, SORT_ASC, $arr); 
        }


        // get link storage

        if($book->domain == 'storage.oblako.kz') {
            $disk = \Storage::build([
                'driver' => 's3',
                'key' => 'O4493_admin',
                'secret' => 'nzxk4iNukQWx',
                'region' => 'us-east-1',
                'bucket' => 'tenantbp',
                'endpoint' => 'https://storage.oblako.kz:443',
                'use_path_style_endpoint' => true,
                'throw' => false,
                'visibility' => 'public'
            ]);
    
            $book->link = $disk->temporaryUrl(
                $book->link, now()->addMinutes(360)
            );
        }

        return [
            'tests' => $arr,
            'activeBook' => $book,
        ];
    }
    
    public function save(Request $request)
    {
        $b = Book::find($request->id);
        $blink = "";

        if($b) {

            if($request->file('file')) {
                $disk = \Storage::build([
                    'driver' => 's3',
                    'key' => 'O4493_admin',
                    'secret' => 'nzxk4iNukQWx',
                    'region' => 'us-east-1',
                    'bucket' => 'tenantbp',
                    'endpoint' => 'https://storage.oblako.kz:443',
                    'use_path_style_endpoint' => true,
                    'throw' => false,
                    'visibility' => 'public'
                ]);

                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $originalFileName = $file->getClientOriginalName();
                $fileName = uniqid() . '_' . md5(time()) . '.' . $extension; // a unique file name

                $path = $disk->putFileAs('/bookcovers', $file, $fileName);

                $b->img = '/bookcovers/' . $fileName;
                
            }

            $b->title = $request->title;
            $b->author = $request->author;
            $b->description = $request->description;
           
            $b->group_id = $request->group_id;
            $b->save();

            $blink = $disk->temporaryUrl(
                $b->img, now()->addMinutes(360)
            );
        
        }

        return $blink;
    }

    public function delete(Request $request) {
        $book = Book::find($request->id);

       
        if($book) {
            if($book->link && $book->link != '') {
                $str = $book->link;
                $replaceWith = '';
                $findStr = '\/storage';
                $link = preg_replace('/' . $findStr . '/', $replaceWith, $str, 1);

                if(\Storage::exists('public/' . $link)){
                    \Storage::delete('public/' . $link);
                }

            }
           
           $book->delete();

        
        }
    }   

    public function update(Request $request)
    {
        $b = Book::find($request->book['id']);
        if($b) {
            $b->title = $request->book['title'];
            $b->author = $request->book['author'];
            $b->description = $request->book['description'];
            $b->group_id = $request->book['group_id'];
            $b->save();



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
