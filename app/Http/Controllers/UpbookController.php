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
        
        // get links

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
        

        foreach ($cats as $xkey => $cat) {
            foreach ($cat->books as $key => $book) {
                if($book->domain == 'storage.oblako.kz') {
                    $book->link = $disk->temporaryUrl(
                        $book->link, now()->addMinutes(360)
                    );
                }
                if($book->img != null && $book->img != '') {
                    $book->img = $disk->temporaryUrl(
                        $book->img, now()->addMinutes(360)
                    );
                }
                
                // dd($book);
                // $cats[$xkey]['books'][$key] = $book;
            }


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

    

    /**
     * Get book and it's tests
     * 
     * @param Request $request
     * 
     * @return array 
     * 
     * return [
     *    'tests' => array,
     *    'activeBook' => Book::class,
     * ]; 
     */
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
        
        if($book->domain == 'storage.oblako.kz') {
          
            
            if($book->link != '' && $book->link != null) {
                $book->link = $disk->temporaryUrl(
                    $book->link, now()->addMinutes(360)
                );
            }   
            

            
        }

        if($book->img != '' && $book->img != null) {
            $book->img = $disk->temporaryUrl(
                $book->img, now()->addMinutes(360)
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
                $links = $this->uploadFile('/bookcovers', $request->file('file')); 
                $blink = $links['temp'];
                $b->img = $links['relative'];
            }

            $b->title = $request->title;
            $b->author = $request->author;
            $b->description = $request->description;
           
            $b->group_id = $request->group_id;
            $b->save();
        
        }

        return $blink;
    }
    
    /**
     * Upload file to S3 and return relative link
     * @param String $path
     * @param mixed $file
     * 
     * @return array 
     * 
     * 'relative' => String
     * 'temp' => String
     */
    private function uploadFile(String $path, $file)
    {
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

        $extension = $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();
        $fileName = uniqid() . '_' . md5(time()) . '.' . $extension; // a unique file name

        $disk->putFileAs($path, $file, $fileName);

        $xpath = $path . '/' . $fileName;
        
        return [
            'relative' => $xpath,
            'temp' => $disk->temporaryUrl(
                $xpath, now()->addMinutes(360)
            )
        ];
    }
    
    /**
     * Delete book
     * 
     * @param Request $request
     * 
     * @return [type]
     */
    public function delete(Request $request) {
        $book = Book::find($request->id);

       
        if($book) {
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

            if($book->link && $book->link != '' && $disk->exists($book->link)) {
                $disk->delete($book->link);
            }

            if($book->img && $book->img != '' && $disk->exists($book->img)) {
                $disk->delete($book->img);
            }
           
            $book->delete();
        }
    }   

    
    /**
     * Update book and save tests
     * 
     * @param Request $request
     * 
     * @return String
     */
    public function update(Request $request)
    {
        $book = json_decode($request->book, true);
        $tests = json_decode($request->tests, true);

        $img_link = '';

        $b = Book::find($book['id']);
        if($b) {

            if($request->file('file')) {

                if($b->img != '' && $b->img != null) {
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
                    
                    if($disk->exists($b->img)) {
                        $disk->delete($b->img);
                    }
                }

                $links = $this->uploadFile('/bookcovers', $request->file('file')); 
                $img_link = $links['temp'];
                $b->img = $links['relative'];
            }

            $b->title = $book['title'];
            $b->author = $book['author'];
            $b->description = $book['description'];
            $b->group_id = $book['group_id'];
            $b->save();



            foreach ($tests as $key => $test) {

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

        return $img_link;

        
    }
    
    
    
}
